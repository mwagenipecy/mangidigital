<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }
        $sales = $organization->sales()
            ->with(['client', 'items.product', 'items.store'])
            ->latest('sale_date')
            ->latest('id')
            ->paginate(15);

        return view('sales.index', ['sales' => $sales]);
    }

    public function create(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }
        $products = $organization->products()->orderBy('name')->get();
        $stores = $organization->stores()->orderBy('name')->get();
        $serviceProviders = $organization->serviceProviders()->orderBy('type')->orderBy('name')->get();

        return view('sales.create', [
            'products' => $products,
            'stores' => $stores,
            'serviceProviders' => $serviceProviders,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $validated = $request->validate([
            'client_id' => ['nullable', 'exists:clients,id'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:50'],
            'sale_date' => ['required', 'date'],
            'delivery_requested' => ['nullable', 'boolean'],
            'delivery_cost' => ['nullable', 'numeric', 'min:0'],
            'delivery_service_provider_id' => ['nullable', 'exists:service_providers,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.product_name_override' => ['nullable', 'string', 'max:255'],
            'items.*.store_id' => ['nullable', 'exists:stores,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ], [], [
            'items.*.quantity' => 'quantity',
            'items.*.unit_price' => 'unit price',
        ]);

        if (! $validated['client_id'] && (empty($validated['client_name']) || empty($validated['client_phone']))) {
            return back()->withErrors(['client_name' => __('Select a client or enter name and phone for new client.')]);
        }

        foreach ($validated['items'] as $i => $item) {
            if (empty($item['product_id']) && empty(trim($item['product_name_override'] ?? ''))) {
                return back()->withErrors(['items' => __('Each row must have a product or an "Other" product name.')]);
            }
        }

        $clientId = $validated['client_id'] ?? null;
        $clientName = $validated['client_name'] ?? null;
        $clientPhone = $validated['client_phone'] ?? null;

        if (! $clientId && $clientName && $clientPhone) {
            $client = $organization->clients()->firstOrCreate(
                ['phone' => $clientPhone],
                ['name' => $clientName]
            );
            $clientId = $client->id;
            if ($client->wasRecentlyCreated && $clientName !== $client->name) {
                $client->update(['name' => $clientName]);
            }
        } elseif ($clientId && ! $organization->clients()->where('id', $clientId)->exists()) {
            return back()->withErrors(['client_id' => __('Invalid client.')]);
        }

        $deliveryRequested = (bool) ($validated['delivery_requested'] ?? false);
        $deliveryCost = $deliveryRequested ? (float) ($validated['delivery_cost'] ?? 0) : 0;
        $deliveryProviderId = $deliveryRequested ? ($validated['delivery_service_provider_id'] ?? null) : null;

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $qty = (float) $item['quantity'];
            $price = (float) $item['unit_price'];
            $subtotal += $qty * $price;
        }
        $total = $subtotal + $deliveryCost;

        $receiptNumber = $this->generateReceiptNumber($organization);

        DB::beginTransaction();
        try {
            $sale = $organization->sales()->create([
                'client_id' => $clientId,
                'client_name' => $clientName,
                'client_phone' => $clientPhone ?? ($clientId ? Client::find($clientId)?->phone : null),
                'sale_date' => $validated['sale_date'],
                'subtotal' => $subtotal,
                'delivery_requested' => $deliveryRequested,
                'delivery_cost' => $deliveryCost,
                'delivery_service_provider_id' => $deliveryProviderId,
                'delivery_status' => $deliveryRequested ? Sale::DELIVERY_STATUS_PENDING : null,
                'total' => $total,
                'receipt_number' => $receiptNumber,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $qty = (float) $item['quantity'];
                $price = (float) $item['unit_price'];
                $lineTotal = $qty * $price;

                $productId = $item['product_id'] ?? null;
                $productNameOverride = $item['product_name_override'] ?? null;
                $storeId = $item['store_id'] ?? null;

                $sale->items()->create([
                    'product_id' => $productId,
                    'product_name_override' => $productNameOverride,
                    'store_id' => $storeId,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'line_total' => $lineTotal,
                ]);

                if ($productId && $storeId && $organization->products()->where('id', $productId)->exists() && $organization->stores()->where('id', $storeId)->exists()) {
                    $inventory = Inventory::where('organization_id', $organization->id)
                        ->where('product_id', $productId)
                        ->where('store_id', $storeId)
                        ->first();
                    if ($inventory && (float) $inventory->quantity >= $qty) {
                        $inventory->quantity = (float) $inventory->quantity - $qty;
                        if ($inventory->quantity <= 0) {
                            $inventory->is_out_of_stock = true;
                            $inventory->quantity = 0;
                        }
                        $inventory->save();

                        InventoryTransaction::create([
                            'inventory_id' => $inventory->id,
                            'type' => InventoryTransaction::TYPE_OUT,
                            'quantity' => $qty,
                            'from_store_id' => $inventory->store_id,
                            'to_store_id' => null,
                            'reference' => 'Sale #' . $sale->id . ' (Receipt ' . $receiptNumber . ')',
                            'user_id' => auth()->id(),
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('sales.show', $sale)->with('success', __('Sale recorded. Receipt ') . $receiptNumber);
    }

    public function show(Sale $sale): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $sale->organization_id !== $organization->id) {
            abort(404);
        }
        $sale->load(['client', 'items.product', 'items.store', 'deliveryServiceProvider', 'createdByUser']);

        return view('sales.show', ['sale' => $sale]);
    }

    public function receipt(Sale $sale): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $sale->organization_id !== $organization->id) {
            abort(404);
        }
        $sale->load(['client', 'items.product', 'items.store', 'deliveryServiceProvider']);

        return view('sales.receipt', ['sale' => $sale]);
    }

    private function generateReceiptNumber($organization): string
    {
        $year = date('Y');
        $prefix = 'REC-' . $year . '-';
        $last = $organization->sales()->whereNotNull('receipt_number')->where('receipt_number', 'like', $prefix . '%')->orderByDesc('id')->first();
        $num = 1;
        if ($last && preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $last->receipt_number, $m)) {
            $num = (int) $m[1] + 1;
        }
        return $prefix . str_pad((string) $num, 5, '0', STR_PAD_LEFT);
    }
}
