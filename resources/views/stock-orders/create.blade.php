@extends('layouts.dashboard')

@section('title', __('New stock order'))

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('stock-orders.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            Back to orders
        </a>
        <h1 class="dash-page-title">New stock order</h1>
        <p class="dash-page-subtitle">International or local — add money paid, transport and other charges, payment and estimated receive date</p>
    </div>
</div>

@if($products->isEmpty())
    <div class="dash-card" style="background:var(--dash-brand-10);border-color:var(--dash-brand);">
        <p style="margin:0;font-size:.9rem;">Add at least one <a href="{{ route('products.index') }}" class="text-[var(--dash-brand)] font-semibold" wire:navigate>product</a> before creating a stock order.</p>
    </div>
@else
@if($errors->any())
    <div class="dash-card" style="margin-bottom:16px;background:rgba(239,68,68,.08);border-color:var(--dash-danger);">
        <ul style="margin:0;padding-left:18px;font-size:.9rem;color:var(--dash-danger);">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="dash-card" style="margin-bottom:20px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Order details</div>
            <div class="dash-card-subtitle">Type, transporter/clearance, payment and charges</div>
        </div>
    </div>
    <form action="{{ route('stock-orders.store') }}" method="POST" id="stock-order-form">
        @csrf
        <div style="padding:0 20px 20px;display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;">
            <div>
                <label for="order_type" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Order type <span style="color:var(--dash-danger);">*</span></label>
                <select id="order_type" name="order_type" required style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                    <option value="international" {{ old('order_type') === 'international' ? 'selected' : '' }}>International</option>
                    <option value="local" {{ old('order_type', 'local') === 'local' ? 'selected' : '' }}>Local</option>
                </select>
            </div>
            <div>
                <label for="service_provider_id" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Transporter / clearance (optional)</label>
                <select id="service_provider_id" name="service_provider_id" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                    <option value="">— None —</option>
                    @foreach($serviceProviders as $sp)
                        <option value="{{ $sp->id }}" {{ old('service_provider_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }} ({{ $sp->type_label }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="amount_paid" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Money paid (TZS)</label>
                <input type="number" id="amount_paid" name="amount_paid" value="{{ old('amount_paid') }}" min="0" step="1" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;" placeholder="0">
            </div>
            <div>
                <label for="transport_charges" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Transport charges (TZS)</label>
                <input type="number" id="transport_charges" name="transport_charges" value="{{ old('transport_charges') }}" min="0" step="1" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;" placeholder="0">
            </div>
            <div>
                <label for="other_charges" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Other charges (TZS)</label>
                <input type="number" id="other_charges" name="other_charges" value="{{ old('other_charges') }}" min="0" step="1" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;" placeholder="0">
            </div>
            <div>
                <label for="payment_date" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Payment date</label>
                <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date') }}" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
            <div>
                <label for="estimated_receive_date" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Estimated receive date</label>
                <input type="date" id="estimated_receive_date" name="estimated_receive_date" value="{{ old('estimated_receive_date') }}" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
        </div>
        <div style="padding:0 20px 12px;">
            <label for="notes" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Notes</label>
            <textarea id="notes" name="notes" rows="2" style="width:100%;max-width:400px;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">{{ old('notes') }}</textarea>
        </div>

        <div class="dash-card-header" style="border-top:1px solid var(--dash-border);margin-top:8px;">
            <div>
                <div class="dash-card-title">Order items</div>
                <div class="dash-card-subtitle">Select product, quantity and order price per unit</div>
            </div>
            <button type="button" id="add-item-btn" class="dash-btn dash-btn-outline" style="font-size:.85rem;">Add row</button>
        </div>
        <div style="padding:0 20px 20px;">
            <table class="dash-table" id="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Order price/unit (TZS)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="items-tbody">
                    <tr class="item-row">
                        <td>
                            <select name="items[0][product_id]" required style="width:100%;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                                <option value="">Select product</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('items.0.product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[0][quantity_ordered]" value="{{ old('items.0.quantity_ordered') }}" min="0.01" step="any" required style="width:100px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                        </td>
                        <td>
                            <input type="number" name="items[0][order_price_per_unit]" value="{{ old('items.0.order_price_per_unit') }}" min="0" step="1" style="width:120px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;" placeholder="0">
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <p style="font-size:.8rem;color:var(--dash-muted);margin-top:8px;">Add at least one product. You can add more rows with "Add row".</p>
            <button type="submit" class="dash-btn dash-btn-brand" style="margin-top:16px;">Create order</button>
        </div>
    </form>
</div>

<script>
document.getElementById('add-item-btn').addEventListener('click', function() {
    const tbody = document.getElementById('items-tbody');
    const rows = tbody.querySelectorAll('.item-row');
    const idx = rows.length;
    const firstSelect = tbody.querySelector('select[name^="items["]');
    const options = firstSelect ? Array.from(firstSelect.options).map(o => ({ value: o.value, text: o.text })) : [];
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = '<td><select name="items[' + idx + '][product_id]" required style="width:100%;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"><option value="">Select product</option>' + options.filter(o => o.value).map(o => '<option value="' + o.value + '">' + o.text + '</option>').join('') + '</select></td><td><input type="number" name="items[' + idx + '][quantity_ordered]" min="0.01" step="any" required style="width:100px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"></td><td><input type="number" name="items[' + idx + '][order_price_per_unit]" min="0" step="1" style="width:120px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;" placeholder="0"></td><td><button type="button" class="dash-btn dash-btn-outline remove-row" style="padding:4px 8px;font-size:.75rem;">Remove</button></td>';
    tbody.appendChild(tr);
    tr.querySelector('.remove-row').addEventListener('click', function() { tr.remove(); });
});
document.getElementById('items-tbody').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-row')) e.target.closest('tr').remove();
});
</script>
@endif
@endsection
