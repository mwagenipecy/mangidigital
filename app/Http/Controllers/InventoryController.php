<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization to manage inventory.'));
        }
        $inventories = $organization->inventories()
            ->with(['product', 'store'])
            ->latest()
            ->paginate(15);

        return view('inventory.index', ['inventories' => $inventories]);
    }

    public function create(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }
        $products = $organization->products()->orderBy('name')->get();
        $stores = $organization->stores()->orderBy('name')->get();

        return view('inventory.create', [
            'products' => $products,
            'stores' => $stores,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'store_id' => ['required', 'exists:stores,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'price_per_unit' => ['nullable', 'numeric', 'min:0'],
            'buying_price_per_unit' => ['nullable', 'numeric', 'min:0'],
        ]);

        if (! $organization->products()->where('id', $validated['product_id'])->exists() ||
            ! $organization->stores()->where('id', $validated['store_id'])->exists()) {
            return back()->withErrors(['product_id' => __('Invalid product or store.')]);
        }

        $inventory = Inventory::firstOrNew([
            'organization_id' => $organization->id,
            'product_id' => $validated['product_id'],
            'store_id' => $validated['store_id'],
        ]);
        $inventory->quantity = ($inventory->quantity ?? 0) + (float) $validated['quantity'];
        $inventory->price_per_unit = $validated['price_per_unit'] ?? $inventory->price_per_unit ?? $inventory->product->price ?? null;
        if (isset($validated['buying_price_per_unit'])) {
            $inventory->buying_price_per_unit = $validated['buying_price_per_unit'];
        }
        $inventory->is_out_of_stock = false;
        $inventory->save();

        if ((float) $validated['quantity'] > 0) {
            InventoryTransaction::create([
                'inventory_id' => $inventory->id,
                'type' => InventoryTransaction::TYPE_IN,
                'quantity' => $validated['quantity'],
                'from_store_id' => null,
                'to_store_id' => $inventory->store_id,
                'reference' => $request->input('reference', 'Initial / Restock'),
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('inventory.index')->with('success', __('Inventory recorded.'));
    }

    public function show(Inventory $inventory): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $inventory->organization_id !== $organization->id) {
            abort(404);
        }
        $inventory->load(['product', 'store']);

        // Must be true chronological order (relationship uses same order; no orderByDesc conflict).
        $chronological = $inventory->transactions()
            ->with(['user', 'fromStore', 'toStore'])
            ->get();

        // Stock before the first transaction (opening) so each line matches:
        // IN: balance = available before + quantity | OUT: available before − quantity.
        $netFromTransactions = 0.0;
        foreach ($chronological as $tx) {
            if ($tx->type === InventoryTransaction::TYPE_IN) {
                $netFromTransactions += (float) $tx->quantity;
            } else {
                $netFromTransactions -= (float) $tx->quantity;
            }
        }
        $running = (float) $inventory->quantity - $netFromTransactions;

        $balanceAfterByTransactionId = [];
        foreach ($chronological as $tx) {
            if ($tx->type === InventoryTransaction::TYPE_IN) {
                $running += (float) $tx->quantity;
            } else {
                $running -= (float) $tx->quantity;
            }
            $balanceAfterByTransactionId[$tx->id] = $running;
        }

        // Newest first in UI; balance per row is still "after this line" in time order.
        $transactions = $chronological->sort(function ($a, $b) {
            $cmp = ($b->created_at?->timestamp ?? 0) <=> ($a->created_at?->timestamp ?? 0);

            return $cmp !== 0 ? $cmp : $b->id <=> $a->id;
        })->values();

        return view('inventory.show', [
            'inventory' => $inventory,
            'transactions' => $transactions,
            'balanceAfterByTransactionId' => $balanceAfterByTransactionId,
        ]);
    }

    public function update(Request $request, Inventory $inventory): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $inventory->organization_id !== $organization->id) {
            abort(404);
        }

        if ($request->has('price_per_unit')) {
            $request->validate(['price_per_unit' => ['nullable', 'numeric', 'min:0']]);
            $inventory->price_per_unit = $request->input('price_per_unit');
        }
        if ($request->has('buying_price_per_unit')) {
            $request->validate(['buying_price_per_unit' => ['nullable', 'numeric', 'min:0']]);
            $inventory->buying_price_per_unit = $request->input('buying_price_per_unit');
        }
        if ($request->has('is_out_of_stock')) {
            $inventory->is_out_of_stock = (bool) $request->input('is_out_of_stock');
            if ($inventory->is_out_of_stock) {
                $inventory->quantity = 0;
            }
        }
        $inventory->save();

        return back()->with('success', __('Inventory updated.'));
    }

    public function addStock(Request $request, Inventory $inventory): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $inventory->organization_id !== $organization->id) {
            abort(404);
        }

        $validated = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'reference' => ['nullable', 'string', 'max:255'],
        ]);

        $qty = (float) $validated['quantity'];
        $inventory->quantity += $qty;
        $inventory->is_out_of_stock = false;
        $inventory->save();

        InventoryTransaction::create([
            'inventory_id' => $inventory->id,
            'type' => InventoryTransaction::TYPE_IN,
            'quantity' => $qty,
            'from_store_id' => null,
            'to_store_id' => $inventory->store_id,
            'reference' => $validated['reference'] ?? 'Restock',
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', __('Stock added.'));
    }

    public function removeStock(Request $request, Inventory $inventory): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $inventory->organization_id !== $organization->id) {
            abort(404);
        }

        $validated = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'reference' => ['nullable', 'string', 'max:255'],
        ]);

        $qty = (float) $validated['quantity'];
        if ($qty > $inventory->quantity) {
            return back()->withErrors(['quantity' => __('Quantity cannot exceed current stock.')]);
        }

        $inventory->quantity -= $qty;
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
            'reference' => $validated['reference'] ?? 'Sale / Use',
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', __('Stock removed.'));
    }
}
