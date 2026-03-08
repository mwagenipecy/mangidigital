<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\StockOrder;
use App\Models\StockOrderActivity;
use App\Models\StockOrderItem;
use App\Models\StockOrderItemReceipt;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockOrderController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization) {
            return redirect()->route('dashboard')->with('error', __('You need an organization.'));
        }

        $base = $organization->stockOrders();
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        $overall = [
            'count' => (clone $base)->count(),
            'amount_paid' => (float) (clone $base)->sum('amount_paid'),
            'transport_charges' => (float) (clone $base)->sum('transport_charges'),
            'other_charges' => (float) (clone $base)->sum('other_charges'),
        ];
        $overall['expenses'] = $overall['transport_charges'] + $overall['other_charges'];
        $overall['total_cost'] = $overall['amount_paid'] + $overall['expenses'];

        $thisMonth = [
            'count' => (clone $base)->whereBetween('created_at', [$startOfMonth, $now])->count(),
            'amount_paid' => (float) (clone $base)->whereBetween('created_at', [$startOfMonth, $now])->sum('amount_paid'),
            'transport_charges' => (float) (clone $base)->whereBetween('created_at', [$startOfMonth, $now])->sum('transport_charges'),
            'other_charges' => (float) (clone $base)->whereBetween('created_at', [$startOfMonth, $now])->sum('other_charges'),
        ];
        $thisMonth['expenses'] = $thisMonth['transport_charges'] + $thisMonth['other_charges'];
        $thisMonth['total_cost'] = $thisMonth['amount_paid'] + $thisMonth['expenses'];

        $thisYear = [
            'count' => (clone $base)->whereBetween('created_at', [$startOfYear, $now])->count(),
            'amount_paid' => (float) (clone $base)->whereBetween('created_at', [$startOfYear, $now])->sum('amount_paid'),
            'transport_charges' => (float) (clone $base)->whereBetween('created_at', [$startOfYear, $now])->sum('transport_charges'),
            'other_charges' => (float) (clone $base)->whereBetween('created_at', [$startOfYear, $now])->sum('other_charges'),
        ];
        $thisYear['expenses'] = $thisYear['transport_charges'] + $thisYear['other_charges'];
        $thisYear['total_cost'] = $thisYear['amount_paid'] + $thisYear['expenses'];

        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            $monthlyRaw = $organization->stockOrders()
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as count, COALESCE(SUM(amount_paid),0) as amount_paid, COALESCE(SUM(transport_charges),0) as transport_charges, COALESCE(SUM(other_charges),0) as other_charges")
                ->where('created_at', '>=', $now->copy()->subMonths(11)->startOfMonth())
                ->groupBy('month_key')
                ->orderBy('month_key')
                ->get()
                ->keyBy('month_key');
        } else {
            $monthlyRaw = collect();
            $organization->stockOrders()
                ->where('created_at', '>=', $now->copy()->subMonths(11)->startOfMonth())
                ->get()
                ->groupBy(fn ($o) => $o->created_at->format('Y-m'))
                ->each(function ($group, $key) use ($monthlyRaw) {
                    $monthlyRaw->put($key, (object) [
                        'month_key' => $key,
                        'count' => $group->count(),
                        'amount_paid' => (float) $group->sum('amount_paid'),
                        'transport_charges' => (float) $group->sum('transport_charges'),
                        'other_charges' => (float) $group->sum('other_charges'),
                    ]);
                });
        }

        $monthly = collect();
        for ($i = 11; $i >= 0; $i--) {
            $d = $now->copy()->subMonths($i);
            $key = $d->format('Y-m');
            $row = $monthlyRaw->get($key);
            $monthly->push([
                'label' => $d->format('M Y'),
                'month_key' => $key,
                'count' => $row ? (int) $row->count : 0,
                'amount_paid' => $row ? (float) $row->amount_paid : 0,
                'transport_charges' => $row ? (float) $row->transport_charges : 0,
                'other_charges' => $row ? (float) $row->other_charges : 0,
                'expenses' => $row ? (float) $row->transport_charges + (float) $row->other_charges : 0,
                'total_cost' => $row ? (float) $row->amount_paid + (float) $row->transport_charges + (float) $row->other_charges : 0,
            ]);
        }

        if ($driver === 'mysql') {
            $yearlyRaw = $organization->stockOrders()
                ->selectRaw("YEAR(created_at) as year_key, COUNT(*) as count, COALESCE(SUM(amount_paid),0) as amount_paid, COALESCE(SUM(transport_charges),0) as transport_charges, COALESCE(SUM(other_charges),0) as other_charges")
                ->groupBy('year_key')
                ->orderByDesc('year_key')
                ->limit(5)
                ->get();
        } else {
            $yearlyRaw = $organization->stockOrders()
                ->get()
                ->groupBy(fn ($o) => $o->created_at->format('Y'))
                ->map(fn ($group, $key) => (object) [
                    'year_key' => (int) $key,
                    'count' => $group->count(),
                    'amount_paid' => (float) $group->sum('amount_paid'),
                    'transport_charges' => (float) $group->sum('transport_charges'),
                    'other_charges' => (float) $group->sum('other_charges'),
                ])
                ->sortKeysDesc()
                ->take(5)
                ->values()
                ->all();
        }

        $yearly = collect($yearlyRaw)->map(function ($row) {
            $row = is_array($row) ? (object) $row : $row;
            $expenses = (float) ($row->transport_charges ?? 0) + (float) ($row->other_charges ?? 0);
            $total = (float) ($row->amount_paid ?? 0) + $expenses;
            return [
                'label' => (string) ($row->year_key ?? ''),
                'count' => (int) ($row->count ?? 0),
                'amount_paid' => (float) ($row->amount_paid ?? 0),
                'expenses' => $expenses,
                'total_cost' => $total,
            ];
        });

        $orders = $organization->stockOrders()
            ->with(['serviceProvider', 'items.product'])
            ->latest()
            ->paginate(15);

        return view('stock-orders.index', [
            'orders' => $orders,
            'overall' => $overall,
            'thisMonth' => $thisMonth,
            'thisYear' => $thisYear,
            'monthly' => $monthly,
            'yearly' => $yearly,
        ]);
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

        return view('stock-orders.create', [
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
            'order_type' => ['required', 'string', 'in:international,local'],
            'service_provider_id' => ['nullable', 'exists:service_providers,id'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'transport_charges' => ['nullable', 'numeric', 'min:0'],
            'other_charges' => ['nullable', 'numeric', 'min:0'],
            'payment_date' => ['nullable', 'date'],
            'estimated_receive_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity_ordered' => ['required', 'numeric', 'min:0.01'],
            'items.*.order_price_per_unit' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($validated['service_provider_id'] && ! $organization->serviceProviders()->where('id', $validated['service_provider_id'])->exists()) {
            return back()->withErrors(['service_provider_id' => __('Invalid service provider.')]);
        }

        foreach ($validated['items'] as $item) {
            if (! $organization->products()->where('id', $item['product_id'])->exists()) {
                return back()->withErrors(['items' => __('Invalid product in items.')]);
            }
        }

        $order = $organization->stockOrders()->create([
            'order_type' => $validated['order_type'],
            'service_provider_id' => $validated['service_provider_id'] ?? null,
            'amount_paid' => $validated['amount_paid'] ?? 0,
            'transport_charges' => $validated['transport_charges'] ?? 0,
            'other_charges' => $validated['other_charges'] ?? 0,
            'payment_date' => $validated['payment_date'] ?? null,
            'estimated_receive_date' => $validated['estimated_receive_date'] ?? null,
            'status' => StockOrder::STATUS_ORDERED,
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['items'] as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity_ordered' => $item['quantity_ordered'],
                'order_price_per_unit' => $item['order_price_per_unit'] ?? 0,
            ]);
        }

        $order->activities()->create([
            'user_id' => auth()->id(),
            'action' => StockOrderActivity::ACTION_CREATED,
            'description' => __('Order created'),
            'changes' => null,
            'created_at' => now(),
        ]);

        return redirect()->route('stock-orders.show', $order)->with('success', __('Stock order created.'));
    }

    public function show(StockOrder $stockOrder): View|RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $stockOrder->organization_id !== $organization->id) {
            abort(404);
        }
        $stockOrder->load([
            'serviceProvider',
            'items.product',
            'items.receipts.store',
            'createdByUser',
            'activities.user',
        ]);
        $stores = $organization->stores()->orderBy('name')->get();

        return view('stock-orders.show', [
            'order' => $stockOrder,
            'stores' => $stores,
        ]);
    }

    public function updateStatus(Request $request, StockOrder $stockOrder): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $stockOrder->organization_id !== $organization->id) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:in_transit,received'],
        ]);

        $oldStatus = $stockOrder->status;
        $stockOrder->status = $validated['status'];
        if ($validated['status'] === StockOrder::STATUS_RECEIVED) {
            $stockOrder->received_at = now();
        }
        $stockOrder->save();

        $statusLabels = [
            StockOrder::STATUS_ORDERED => 'Ordered',
            StockOrder::STATUS_IN_TRANSIT => 'In transit',
            StockOrder::STATUS_RECEIVED => 'Received',
        ];
        $stockOrder->activities()->create([
            'user_id' => auth()->id(),
            'action' => StockOrderActivity::ACTION_STATUS_CHANGED,
            'description' => __('Status changed to :status', ['status' => $statusLabels[$validated['status']] ?? $validated['status']]),
            'changes' => ['status' => ['old' => $oldStatus, 'new' => $validated['status']]],
            'created_at' => now(),
        ]);

        return back()->with('success', __('Order status updated.'));
    }

    public function update(Request $request, StockOrder $stockOrder): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $stockOrder->organization_id !== $organization->id) {
            abort(404);
        }

        $validated = $request->validate([
            'amount_paid' => ['nullable', 'numeric'],
            'transport_charges' => ['nullable', 'numeric', 'min:0'],
            'other_charges' => ['nullable', 'numeric', 'min:0'],
            'payment_date' => ['nullable', 'date'],
            'estimated_receive_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $stockOrder->amount_paid = $request->input('amount_paid') !== null && $request->input('amount_paid') !== '' ? (float) $request->input('amount_paid') : $stockOrder->amount_paid;
        $stockOrder->transport_charges = $request->input('transport_charges') !== null && $request->input('transport_charges') !== '' ? (float) $request->input('transport_charges') : $stockOrder->transport_charges;
        $stockOrder->other_charges = $request->input('other_charges') !== null && $request->input('other_charges') !== '' ? (float) $request->input('other_charges') : $stockOrder->other_charges;
        $old = [
            'amount_paid' => (float) $stockOrder->amount_paid,
            'transport_charges' => (float) $stockOrder->transport_charges,
            'other_charges' => (float) $stockOrder->other_charges,
            'payment_date' => $stockOrder->payment_date?->format('Y-m-d'),
            'estimated_receive_date' => $stockOrder->estimated_receive_date?->format('Y-m-d'),
        ];
        $stockOrder->payment_date = $request->filled('payment_date') ? $validated['payment_date'] : $stockOrder->payment_date;
        $stockOrder->estimated_receive_date = $request->filled('estimated_receive_date') ? $validated['estimated_receive_date'] : $stockOrder->estimated_receive_date;
        $stockOrder->notes = $request->input('notes', $stockOrder->notes);
        $stockOrder->save();

        $new = [
            'amount_paid' => (float) $stockOrder->amount_paid,
            'transport_charges' => (float) $stockOrder->transport_charges,
            'other_charges' => (float) $stockOrder->other_charges,
            'payment_date' => $stockOrder->payment_date?->format('Y-m-d'),
            'estimated_receive_date' => $stockOrder->estimated_receive_date?->format('Y-m-d'),
        ];
        $changes = [];
        foreach (['amount_paid', 'transport_charges', 'other_charges', 'payment_date', 'estimated_receive_date'] as $key) {
            if ($old[$key] != $new[$key]) {
                $changes[$key] = ['old' => $old[$key], 'new' => $new[$key]];
            }
        }
        if (! empty($changes)) {
            $stockOrder->activities()->create([
                'user_id' => auth()->id(),
                'action' => StockOrderActivity::ACTION_CHARGES_UPDATED,
                'description' => __('Charges or refund updated'),
                'changes' => $changes,
                'created_at' => now(),
            ]);
        }

        return back()->with('success', __('Charges and payment updated.'));
    }

    public function receive(Request $request, StockOrder $stockOrder): RedirectResponse
    {
        $organization = auth()->user()->organization;
        if (! $organization || $stockOrder->organization_id !== $organization->id) {
            abort(404);
        }
        if ($stockOrder->status !== StockOrder::STATUS_RECEIVED) {
            return back()->with('error', __('Mark the order as received first.'));
        }

        $validated = $request->validate([
            'receipts' => ['required', 'array', 'min:1'],
            'receipts.*.stock_order_item_id' => ['required', 'exists:stock_order_items,id'],
            'receipts.*.store_id' => ['required', 'exists:stores,id'],
            'receipts.*.quantity_received' => ['required', 'numeric', 'min:0.01'],
        ]);

        $orderItemIds = $stockOrder->items()->pluck('id')->all();
        foreach ($validated['receipts'] as $r) {
            if (! in_array($r['stock_order_item_id'], $orderItemIds, true)) {
                return back()->withErrors(['receipts' => __('Invalid order item.')]);
            }
            if (! $organization->stores()->where('id', $r['store_id'])->exists()) {
                return back()->withErrors(['receipts' => __('Invalid store.')]);
            }
        }

        $receiptLines = [];
        foreach ($validated['receipts'] as $r) {
            $item = StockOrderItem::find($r['stock_order_item_id']);
            $item->load('product');
            $pending = $item->quantityPending();
            $qty = (float) $r['quantity_received'];
            if ($qty > $pending) {
                return back()->withErrors(['receipts' => __('Quantity for :product exceeds pending.', ['product' => $item->product->name])]);
            }

            $store = $organization->stores()->find($r['store_id']);
            StockOrderItemReceipt::create([
                'stock_order_item_id' => $item->id,
                'store_id' => $r['store_id'],
                'quantity_received' => $qty,
                'received_by' => auth()->id(),
            ]);

            $receiptLines[] = $item->product->name . ': ' . number_format($qty, 0) . ' → ' . ($store->name ?? '—');

            $inventory = Inventory::firstOrNew([
                'organization_id' => $organization->id,
                'product_id' => $item->product_id,
                'store_id' => $r['store_id'],
            ]);
            $inventory->quantity = ($inventory->quantity ?? 0) + $qty;
            $inventory->price_per_unit = $inventory->price_per_unit ?? $item->order_price_per_unit ?? $item->product->price ?? null;
            $inventory->is_out_of_stock = false;
            $inventory->save();

            InventoryTransaction::create([
                'inventory_id' => $inventory->id,
                'type' => InventoryTransaction::TYPE_IN,
                'quantity' => $qty,
                'from_store_id' => null,
                'to_store_id' => $inventory->store_id,
                'reference' => 'Stock order #' . $stockOrder->id . ' received',
                'user_id' => auth()->id(),
            ]);
        }

        $stockOrder->activities()->create([
            'user_id' => auth()->id(),
            'action' => StockOrderActivity::ACTION_RECEIVED_TO_INVENTORY,
            'description' => __('Stock received to inventory'),
            'changes' => ['lines' => $receiptLines],
            'created_at' => now(),
        ]);

        return back()->with('success', __('Stock received and added to inventory.'));
    }
}
