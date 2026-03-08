@extends('layouts.dashboard')

@section('title', __('Stock order') . ' #' . $order->id)

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('stock-orders.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            Back to orders
        </a>
        <h1 class="dash-page-title">Stock order #{{ $order->id }}</h1>
        <p class="dash-page-subtitle">{{ $order->order_type_label }} — {{ $order->serviceProvider?->name ?? 'No transporter' }}</p>
    </div>
</div>

@if(session('error'))
    <div class="dash-card" style="margin-bottom:16px;background:rgba(239,68,68,.08);border-color:var(--dash-danger);">
        <p style="margin:0;font-size:.9rem;color:var(--dash-danger);">{{ session('error') }}</p>
    </div>
@endif
@if(session('success'))
    <div class="dash-card" style="margin-bottom:16px;background:var(--dash-brand-10);border-color:var(--dash-brand);">
        <p style="margin:0;font-size:.9rem;color:var(--dash-ink);">{{ session('success') }}</p>
    </div>
@endif
@if($errors->any())
    <div class="dash-card" style="margin-bottom:16px;background:rgba(239,68,68,.08);border-color:var(--dash-danger);">
        <ul style="margin:0;padding-left:18px;font-size:.9rem;color:var(--dash-danger);">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- Status & actions --}}
<div class="dash-card" style="margin-bottom:20px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Order info</div>
            <div class="dash-card-subtitle">Payment date, estimated receive, status — monitor cargo until it reaches you</div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            @if($order->status === \App\Models\StockOrder::STATUS_ORDERED)
                <span class="dash-pill" style="background:#e0f2fe;color:#0369a1;">Ordered</span>
                <form action="{{ route('stock-orders.update-status', $order) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="in_transit">
                    <button type="submit" class="dash-btn dash-btn-brand" style="font-size:.85rem;">Set in transit</button>
                </form>
            @elseif($order->status === \App\Models\StockOrder::STATUS_IN_TRANSIT)
                <span class="dash-pill" style="background:#fef3c7;color:#b45309;">In transit</span>
                <form action="{{ route('stock-orders.update-status', $order) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="received">
                    <button type="submit" class="dash-btn dash-btn-brand" style="font-size:.85rem;">Mark as received</button>
                </form>
            @else
                <span class="dash-pill dash-pill-green">Received</span>
                @if($order->received_at)
                    <span class="dash-td-sub">Received at {{ $order->received_at->format('d M Y H:i') }}</span>
                @endif
            @endif
        </div>
    </div>
    <div style="padding:0 20px 20px;display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px;">
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:2px;">Payment date</div>
            <div style="font-weight:600;">{{ $order->payment_date?->format('d M Y') ?? '—' }}</div>
        </div>
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:2px;">Estimated receive</div>
            <div style="font-weight:600;">{{ $order->estimated_receive_date?->format('d M Y') ?? '—' }}</div>
        </div>
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:2px;">Money paid (TZS)</div>
            <div style="font-weight:600;">{{ number_format($order->amount_paid, 0) }}</div>
        </div>
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:2px;">Transport charges (TZS)</div>
            <div style="font-weight:600;">{{ number_format($order->transport_charges, 0) }}</div>
        </div>
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:2px;">Other charges (TZS)</div>
            <div style="font-weight:600;">{{ number_format($order->other_charges, 0) }}</div>
        </div>
    </div>
    @if($order->notes)
        <div style="padding:0 20px 20px;border-top:1px solid var(--dash-border);">
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;">Notes</div>
            <p style="margin:0;font-size:.9rem;">{{ $order->notes }}</p>
        </div>
    @endif
</div>

{{-- Update charges / refund (any time) --}}
<div class="dash-card" style="margin-bottom:20px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Charges & refund</div>
            <div class="dash-card-subtitle">Add charges or update refund (money paid) at any time</div>
        </div>
    </div>
    <form action="{{ route('stock-orders.update', $order) }}" method="POST" style="padding:0 20px 20px;">
        @csrf
        @method('PATCH')
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;">
            <div>
                <label for="amount_paid" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Money paid (TZS)</label>
                <input type="number" id="amount_paid" name="amount_paid" value="{{ old('amount_paid', $order->amount_paid) }}" step="1" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;" placeholder="Reduce for refund">
                @error('amount_paid')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="transport_charges" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Transport charges (TZS)</label>
                <input type="number" id="transport_charges" name="transport_charges" value="{{ old('transport_charges', $order->transport_charges) }}" min="0" step="1" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                @error('transport_charges')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="other_charges" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Other charges (TZS)</label>
                <input type="number" id="other_charges" name="other_charges" value="{{ old('other_charges', $order->other_charges) }}" min="0" step="1" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                @error('other_charges')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="payment_date" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Payment date</label>
                <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', $order->payment_date?->format('Y-m-d')) }}" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
            <div>
                <label for="estimated_receive_date" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Estimated receive</label>
                <input type="date" id="estimated_receive_date" name="estimated_receive_date" value="{{ old('estimated_receive_date', $order->estimated_receive_date?->format('Y-m-d')) }}" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
        </div>
        <div style="margin-top:16px;">
            <label for="notes" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Notes</label>
            <textarea id="notes" name="notes" rows="2" style="width:100%;max-width:500px;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">{{ old('notes', $order->notes) }}</textarea>
        </div>
        <button type="submit" class="dash-btn dash-btn-brand" style="margin-top:16px;">Update charges / refund</button>
    </form>
</div>

{{-- Changelog / activity log --}}
<div class="dash-card" style="margin-bottom:20px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Changelog</div>
            <div class="dash-card-subtitle">All updates on this order — who changed what and when</div>
        </div>
    </div>
    <div style="padding:0 20px 20px;">
        @forelse($order->activities as $activity)
        <div style="padding:12px 0;border-bottom:1px solid var(--dash-border);display:flex;flex-wrap:wrap;gap:8px;align-items:flex-start;">
            <div style="flex:1;min-width:0;">
                <span style="font-weight:600;font-size:.9rem;">{{ $activity->description }}</span>
                @if($activity->changes)
                    <div style="margin-top:6px;font-size:.85rem;color:var(--dash-muted);">
                        @php
                            $parts = [];
                            if (isset($activity->changes['status'])) {
                                $parts[] = 'Status: ' . ($activity->changes['status']['old'] ?? '—') . ' → ' . ($activity->changes['status']['new'] ?? '—');
                            }
                            if (isset($activity->changes['amount_paid'])) {
                                $parts[] = 'Money paid: ' . number_format($activity->changes['amount_paid']['old'] ?? 0, 0) . ' → ' . number_format($activity->changes['amount_paid']['new'] ?? 0, 0) . ' TZS';
                            }
                            if (isset($activity->changes['transport_charges'])) {
                                $parts[] = 'Transport: ' . number_format($activity->changes['transport_charges']['old'] ?? 0, 0) . ' → ' . number_format($activity->changes['transport_charges']['new'] ?? 0, 0) . ' TZS';
                            }
                            if (isset($activity->changes['other_charges'])) {
                                $parts[] = 'Other charges: ' . number_format($activity->changes['other_charges']['old'] ?? 0, 0) . ' → ' . number_format($activity->changes['other_charges']['new'] ?? 0, 0) . ' TZS';
                            }
                            if (isset($activity->changes['payment_date'])) {
                                $parts[] = 'Payment date: ' . ($activity->changes['payment_date']['old'] ?? '—') . ' → ' . ($activity->changes['payment_date']['new'] ?? '—');
                            }
                            if (isset($activity->changes['estimated_receive_date'])) {
                                $parts[] = 'Est. receive: ' . ($activity->changes['estimated_receive_date']['old'] ?? '—') . ' → ' . ($activity->changes['estimated_receive_date']['new'] ?? '—');
                            }
                        @endphp
                        @if(!empty($parts))
                            <span>{{ implode(' · ', $parts) }}</span>
                        @endif
                        @if(isset($activity->changes['lines']))
                            <ul style="margin:6px 0 0;padding-left:18px;">
                                @foreach($activity->changes['lines'] as $line)
                                    <li>{{ $line }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
            </div>
            <div style="font-size:.8rem;color:var(--dash-muted);white-space:nowrap;">
                {{ $activity->user?->name ?? __('System') }} · {{ $activity->created_at?->format('d M Y H:i') ?? '—' }}
            </div>
        </div>
        @empty
        <p style="margin:0;color:var(--dash-muted);font-size:.9rem;">No changes recorded yet.</p>
        @endforelse
    </div>
</div>

{{-- Order items --}}
<div class="dash-card" style="margin-bottom:20px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Order items</div>
            <div class="dash-card-subtitle">Product, quantity ordered, order price — receive to store below</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity ordered</th>
                    <th>Order price/unit</th>
                    <th>Received</th>
                    <th>Pending</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td><span class="dash-td-main">{{ $item->product->name ?? '—' }}</span></td>
                    <td><span class="dash-td-amount">{{ number_format($item->quantity_ordered, 0) }} {{ $item->product->unit ?? '' }}</span></td>
                    <td><span class="dash-td-sub">{{ number_format($item->order_price_per_unit, 0) }} TZS</span></td>
                    <td><span class="dash-td-sub">{{ number_format($item->quantityReceived(), 0) }}</span></td>
                    <td><span class="dash-td-amount">{{ number_format($item->quantityPending(), 0) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Receive to inventory (when status = received and there is pending) --}}
@php
    $itemsWithPending = $order->items->filter(fn ($i) => $i->quantityPending() > 0);
@endphp
@if($order->status === \App\Models\StockOrder::STATUS_RECEIVED && $itemsWithPending->isNotEmpty())
<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Receive to inventory</div>
            <div class="dash-card-subtitle">Select store and quantity — will appear on Inventory page</div>
        </div>
    </div>
    <form action="{{ route('stock-orders.receive', $order) }}" method="POST" style="padding:0 20px 20px;">
        @csrf
        <table class="dash-table" id="receive-table">
            <thead>
                <tr>
                    <th>Product (item)</th>
                    <th>Store / shop</th>
                    <th>Quantity to add</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="receive-tbody">
                <tr class="receive-row">
                    <td>
                        <select name="receipts[0][stock_order_item_id]" required style="width:100%;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                            <option value="">Select product</option>
                            @foreach($itemsWithPending as $it)
                                <option value="{{ $it->id }}">{{ $it->product->name }} (pending: {{ number_format($it->quantityPending(), 0) }})</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="receipts[0][store_id]" required style="width:100%;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                            <option value="">Select store</option>
                            @foreach($stores as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="receipts[0][quantity_received]" min="0.01" step="any" required style="width:100px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <p style="font-size:.8rem;color:var(--dash-muted);margin-top:8px;">Add one or more rows. Each row adds quantity to the selected store and will show in Inventory.</p>
        <button type="button" id="add-receive-row" class="dash-btn dash-btn-outline" style="margin-top:12px;font-size:.85rem;">Add row</button>
        <button type="submit" class="dash-btn dash-btn-brand" style="margin-top:12px;margin-left:8px;">Receive to inventory</button>
    </form>
</div>
<script>
(function() {
    const tbody = document.getElementById('receive-tbody');
    const itemOptions = @json($itemsWithPending->map(fn ($i) => ['id' => $i->id, 'label' => $i->product->name . ' (pending: ' . number_format($i->quantityPending(), 0) . ')'])->values()->all());
    const storeOptions = @json($stores->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values()->all());
    document.getElementById('add-receive-row').addEventListener('click', function() {
        const idx = tbody.querySelectorAll('.receive-row').length;
        const tr = document.createElement('tr');
        tr.className = 'receive-row';
        tr.innerHTML = '<td><select name="receipts[' + idx + '][stock_order_item_id]" required style="width:100%;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"><option value="">Select product</option>' + itemOptions.map(o => '<option value="' + o.id + '">' + o.label + '</option>').join('') + '</select></td><td><select name="receipts[' + idx + '][store_id]" required style="width:100%;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"><option value="">Select store</option>' + storeOptions.map(o => '<option value="' + o.id + '">' + o.name + '</option>').join('') + '</select></td><td><input type="number" name="receipts[' + idx + '][quantity_received]" min="0.01" step="any" required style="width:100px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"></td><td><button type="button" class="remove-receive-row dash-btn dash-btn-outline" style="padding:4px 8px;font-size:.75rem;">Remove</button></td>';
        tbody.appendChild(tr);
        tr.querySelector('.remove-receive-row').addEventListener('click', function() { tr.remove(); });
    });
    tbody.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-receive-row')) e.target.closest('tr').remove();
    });
})();
</script>
</div>
@endif

{{-- Already received (receipts summary) --}}
@if($order->items->flatMap->receipts->isNotEmpty())
<div class="dash-card" style="margin-top:20px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Received into inventory</div>
            <div class="dash-card-subtitle">Quantities added to stores — visible on Inventory page</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Store</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    @foreach($item->receipts as $rec)
                    <tr>
                        <td><span class="dash-td-main">{{ $item->product->name ?? '—' }}</span></td>
                        <td><span class="dash-td-sub">{{ $rec->store->name ?? '—' }}</span></td>
                        <td><span class="dash-td-amount">{{ number_format($rec->quantity_received, 0) }}</span></td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
