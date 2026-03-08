<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\StockReturn;
use App\Models\StockReturnItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockReturnController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }
        $returns = $organization->stockReturns()
            ->with(['sale', 'items.product', 'items.store', 'createdByUser'])
            ->latest('return_date')
            ->latest('id')
            ->paginate(20);

        return view('stock-returns.index', ['returns' => $returns]);
    }

    public function create(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }
        $products = $organization->products()->orderBy('name')->get();
        $stores = $organization->stores()->orderBy('name')->get();
        $sales = $organization->sales()->with('items.product')->latest('sale_date')->limit(100)->get();

        return view('stock-returns.create', [
            'products' => $products,
            'stores' => $stores,
            'sales' => $sales,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $validated = $request->validate([
            'sale_id' => ['nullable', 'exists:sales,id'],
            'return_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.product_name_override' => ['nullable', 'string', 'max:255'],
            'items.*.store_id' => ['required', 'exists:stores,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.reason' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validated['sale_id'] && ! $organization->sales()->where('id', $validated['sale_id'])->exists()) {
            return back()->withErrors(['sale_id' => __('Invalid sale.')]);
        }
        foreach ($validated['items'] as $item) {
            if ($item['store_id'] && ! $organization->stores()->where('id', $item['store_id'])->exists()) {
                return back()->withErrors(['items' => __('Invalid store.')]);
            }
        }

        DB::beginTransaction();
        try {
            $stockReturn = $organization->stockReturns()->create([
                'sale_id' => $validated['sale_id'] ?? null,
                'return_date' => $validated['return_date'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $productId = $item['product_id'] ?? null;
                $storeId = $item['store_id'];
                $qty = (float) $item['quantity'];

                $stockReturn->items()->create([
                    'product_id' => $productId,
                    'product_name_override' => $item['product_name_override'] ?? null,
                    'store_id' => $storeId,
                    'quantity' => $qty,
                    'reason' => $item['reason'] ?? null,
                ]);

                if ($productId && $organization->products()->where('id', $productId)->exists()) {
                    $inventory = Inventory::firstOrNew([
                        'organization_id' => $organization->id,
                        'product_id' => $productId,
                        'store_id' => $storeId,
                    ]);
                    $inventory->quantity = ($inventory->quantity ?? 0) + $qty;
                    $inventory->is_out_of_stock = false;
                    $inventory->save();

                    InventoryTransaction::create([
                        'inventory_id' => $inventory->id,
                        'type' => InventoryTransaction::TYPE_IN,
                        'quantity' => $qty,
                        'from_store_id' => null,
                        'to_store_id' => $storeId,
                        'reference' => 'Return #' . $stockReturn->id,
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()->route('stock-returns.index')->with('success', __('Return recorded. Stock has been added back to inventory.'));
    }
}
