<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ServiceProvider;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $range = $this->resolveDateRange($request);

        $baseSalesQuery = $organization->sales()
            ->whereBetween('sale_date', [$range['from'], $range['to']]);

        $sales = (clone $baseSalesQuery)
            ->with(['client', 'items.product', 'items.store'])
            ->latest('sale_date')
            ->latest('id')
            ->paginate(15);

        $sales->appends($request->query());

        $totalSales = (float) (clone $baseSalesQuery)->sum('total');
        $salesCount = (clone $baseSalesQuery)->count();

        $cogs = (float) SaleItem::query()
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin('inventories', function ($join) {
                $join->on('sale_items.product_id', '=', 'inventories.product_id')
                    ->on('sale_items.store_id', '=', 'inventories.store_id');
            })
            ->where('sales.organization_id', $organization->id)
            ->whereBetween('sales.sale_date', [$range['from'], $range['to']])
            ->selectRaw('COALESCE(SUM(sale_items.quantity * COALESCE(inventories.buying_price_per_unit, 0)), 0) as cogs')
            ->value('cogs');

        $netProfit = $totalSales - $cogs;

        return view('sales.index', [
            'sales' => $sales,
            'range' => $range,
            'stats' => [
                'total_sales' => $totalSales,
                'sales_count' => $salesCount,
                'cogs' => $cogs,
                'net_profit' => $netProfit,
            ],
        ]);
    }

    private function resolveDateRange(Request $request): array
    {
        $preset = $request->input('range', 'this_month');
        $today = CarbonImmutable::today();

        switch ($preset) {
            case 'today':
                $from = $today;
                $to = $today->endOfDay();
                break;
            case 'this_week':
                $from = $today->startOfWeek();
                $to = $today->endOfWeek();
                break;
            case 'last_month':
                $from = $today->subMonth()->startOfMonth();
                $to = $today->subMonth()->endOfMonth();
                break;
            case 'custom':
                $from = $request->filled('from')
                    ? CarbonImmutable::parse($request->input('from'))->startOfDay()
                    : $today->startOfMonth();
                $to = $request->filled('to')
                    ? CarbonImmutable::parse($request->input('to'))->endOfDay()
                    : $today->endOfDay();
                break;
            case 'this_month':
            default:
                $from = $today->startOfMonth();
                $to = $today->endOfDay();
                break;
        }

        return [
            'preset' => $preset,
            'from' => $from,
            'to' => $to,
            'from_display' => $from->format('d M Y'),
            'to_display' => $to->format('d M Y'),
        ];
    }

    public function create(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }
        $categories = $organization->productCategories()->orderBy('name')->get();
        $clients = $organization->clients()->orderBy('name')->get();
        $stores = $organization->stores()->orderBy('name')->get();

        $inventories = Inventory::query()
            ->where('organization_id', $organization->id)
            ->where('quantity', '>', 0)
            ->where('is_out_of_stock', false)
            ->with(['product.productCategory', 'store'])
            ->get();

        $saleInventoryPayload = $inventories->map(function (Inventory $inv) {
            $product = $inv->product;
            $unitPrice = $inv->price_per_unit !== null
                ? (float) $inv->price_per_unit
                : (float) ($product->price ?? 0);

            return [
                'inventory_id' => $inv->id,
                'product_id' => $inv->product_id,
                'product_name' => $product->name,
                'unit' => $product->unit ?? '',
                'category_id' => $product->product_category_id,
                'store_id' => $inv->store_id,
                'store_name' => $inv->store->name,
                'qty_available' => (float) $inv->quantity,
                'unit_price' => $unitPrice,
            ];
        })->values();

        $serviceProvidersPayload = $organization->serviceProviders()
            ->orderBy('type')
            ->orderBy('name')
            ->get()
            ->map(fn (ServiceProvider $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'type' => $p->type,
                'type_label' => $p->type_label,
                'product_category_id' => $p->product_category_id,
            ])
            ->values();

        return view('sales.create', [
            'categories' => $categories,
            'clients' => $clients,
            'stores' => $stores,
            'saleInventoryPayload' => $saleInventoryPayload,
            'serviceProvidersPayload' => $serviceProvidersPayload,
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
            'delivery_scope' => ['nullable', 'in:local,international'],
            'delivery_category_filter_id' => ['nullable', Rule::exists('product_categories', 'id')->where('organization_id', $organization->id)],
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

        if ($deliveryRequested && $deliveryProviderId) {
            $sp = ServiceProvider::where('organization_id', $organization->id)
                ->where('id', $deliveryProviderId)
                ->first();
            if (! $sp) {
                return back()->withErrors(['delivery_service_provider_id' => __('Invalid transport.')]);
            }
            $scope = $validated['delivery_scope'] ?? null;
            if (! $scope) {
                return back()->withErrors(['delivery_scope' => __('Select local or international delivery.')]);
            }
            if ($scope === 'local' && $sp->type !== ServiceProvider::TYPE_LOCAL_TRANSPORT) {
                return back()->withErrors(['delivery_service_provider_id' => __('Selected transport does not match local delivery.')]);
            }
            if ($scope === 'international' && ! in_array($sp->type, [ServiceProvider::TYPE_INTERNATIONAL_TRANSPORT, ServiceProvider::TYPE_CLEARANCE_FORWARDING], true)) {
                return back()->withErrors(['delivery_service_provider_id' => __('Selected transport does not match international delivery.')]);
            }
            $filterCat = $validated['delivery_category_filter_id'] ?? null;
            if ($sp->product_category_id !== null && $filterCat !== null && $filterCat !== '' && (int) $filterCat !== (int) $sp->product_category_id) {
                return back()->withErrors(['delivery_service_provider_id' => __('Selected transport does not match the category filter.')]);
            }
        }

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $qty = (float) $item['quantity'];
            $price = (float) $item['unit_price'];
            $subtotal += $qty * $price;
        }
        $total = $subtotal + $deliveryCost;

        DB::beginTransaction();
        try {
            $sale = null;
            $receiptNumber = null;

            for ($receiptAttempt = 0; $receiptAttempt < 50; $receiptAttempt++) {
                $receiptNumber = $this->generateReceiptNumber();
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
                    break;
                } catch (UniqueConstraintViolationException $e) {
                    if (! $this->isDuplicateReceiptNumberViolation($e)) {
                        throw $e;
                    }
                }
            }

            if ($sale === null) {
                throw new \RuntimeException('Unable to allocate a unique receipt number.');
            }

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
                            'reference' => 'Sale #'.$sale->id.' (Receipt '.$receiptNumber.')',
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

        return redirect()->route('sales.show', $sale)->with('success', __('Sale recorded. Receipt ').$receiptNumber);
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

    private function generateReceiptNumber(): string
    {
        $year = date('Y');
        $prefix = 'REC-'.$year.'-';
        $startPos = strlen($prefix) + 1;

        $maxSuffix = Sale::query()
            ->whereNotNull('receipt_number')
            ->where('receipt_number', 'like', $prefix.'%')
            ->selectRaw('MAX(CAST(SUBSTRING(receipt_number, ?) AS UNSIGNED)) as max_n', [$startPos])
            ->value('max_n');

        $num = (int) ($maxSuffix ?? 0) + 1;

        for ($i = 0; $i < 1000; $i++) {
            $candidate = $prefix.str_pad((string) $num, 5, '0', STR_PAD_LEFT);
            if (! Sale::query()->where('receipt_number', $candidate)->exists()) {
                return $candidate;
            }
            $num++;
        }

        throw new \RuntimeException('Unable to allocate a unique receipt number.');
    }

    private function isDuplicateReceiptNumberViolation(UniqueConstraintViolationException $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, 'receipt_number');
    }
}
