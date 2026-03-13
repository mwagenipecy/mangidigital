@extends('layouts.dashboard')

@section('title', 'Record return')

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('stock-returns.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            Back to returns
        </a>
        <h1 class="dash-page-title">Record return</h1>
        <p class="dash-page-subtitle">Link to sale (optional) — items returned to store; stock is added back to inventory</p>
    </div>
</div>

@if($errors->any())
    <div class="dash-card" style="margin-bottom:16px;background:rgba(239,68,68,.08);border-color:var(--dash-danger);">
        <ul style="margin:0;padding-left:18px;font-size:.9rem;color:var(--dash-danger);">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form action="{{ route('stock-returns.store') }}" method="POST" id="return-form">
    @csrf

    <div class="dash-card dash-form-card">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">Return details</div>
                <div class="dash-card-subtitle">Optional: link to a sale and add notes</div>
            </div>
        </div>
        <div class="dash-form-section">
            <div class="dash-form-grid dash-form-grid--2">
                <div class="dash-form-field">
                    <label for="sale_id">Sale (optional)</label>
                    <select id="sale_id" name="sale_id">
                        <option value="">— None —</option>
                        @foreach($sales as $s)
                            <option value="{{ $s->id }}" {{ old('sale_id') == $s->id ? 'selected' : '' }}>{{ $s->receipt_number ?? '#' . $s->id }} — {{ $s->sale_date?->format('d M Y') }} ({{ $s->display_client_name }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="dash-form-field">
                    <label for="return_date">Return date <span style="color:var(--dash-danger);">*</span></label>
                    <input type="date" id="return_date" name="return_date" value="{{ old('return_date', date('Y-m-d')) }}" required>
                </div>
            </div>
            <div class="dash-form-field" style="margin-top:4px;">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="2" placeholder="Optional notes">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="dash-card dash-form-card">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">Items returned</div>
                <div class="dash-card-subtitle">Product, store to return to, quantity — stock will be added back to inventory</div>
            </div>
            <button type="button" id="add-return-item" class="dash-btn dash-btn-outline">Add row</button>
        </div>
        <div class="dash-form-section">
            <div class="dash-sale-items-wrap">
                <table class="dash-table" id="return-items-table">
                    <thead>
                        <tr>
                            <th class="dash-sale-item-product">Product / name</th>
                            <th class="dash-sale-item-store">Store (return to)</th>
                            <th class="dash-sale-item-qty">Quantity</th>
                            <th style="min-width:120px;">Reason</th>
                            <th class="dash-sale-item-actions"></th>
                        </tr>
                    </thead>
                    <tbody id="return-items-tbody">
                        <tr class="return-item-row">
                            <td class="dash-sale-item-product">
                                <select name="items[0][product_id]" class="return-product">
                                    <option value="">— Other —</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="items[0][product_name_override]" class="return-name-override" style="margin-top:6px;display:none;" placeholder="Product name (when other)">
                            </td>
                            <td class="dash-sale-item-store">
                                <select name="items[0][store_id]" required>
                                    <option value="">Select store</option>
                                    @foreach($stores as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="dash-sale-item-qty"><input type="number" name="items[0][quantity]" value="{{ old('items.0.quantity') }}" min="0.01" step="any" required></td>
                            <td><input type="text" name="items[0][reason]" value="{{ old('items.0.reason') }}" placeholder="Optional"></td>
                            <td class="dash-sale-item-actions"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="dash-form-hint">Only items with a product from your catalog will update inventory. “Other” is for record only.</p>
            <div class="dash-form-actions">
                <button type="submit" class="dash-btn dash-btn-brand">
                    <flux:icon.check class="size-4" />
                    Save return
                </button>
            </div>
        </div>
    </div>
</form>

<script>
document.querySelectorAll('.return-product').forEach(function(sel) {
    sel.addEventListener('change', function() {
        var row = this.closest('tr');
        var ov = row.querySelector('.return-name-override');
        if (this.value === '') { if (ov) ov.style.display = 'block'; } else { if (ov) ov.style.display = 'none'; }
    });
});
document.getElementById('add-return-item').addEventListener('click', function() {
    var tbody = document.getElementById('return-items-tbody');
    var idx = tbody.querySelectorAll('.return-item-row').length;
    var first = tbody.querySelector('.return-item-row');
    var productOpts = Array.from(first.querySelector('.return-product').options).map(function(o) { return { v: o.value, t: o.text }; }).filter(function(x) { return x.v; });
    var storeOpts = Array.from(first.querySelector('select[name^="items"][name*="store_id"]').options).map(function(o) { return { v: o.value, t: o.text }; }).filter(function(x) { return x.v; });
    var tr = document.createElement('tr');
    tr.className = 'return-item-row';
    var productHtml = '<select name="items[' + idx + '][product_id]" class="return-product"><option value="">— Other —</option>';
    productOpts.forEach(function(o) { productHtml += '<option value="' + o.v + '">' + o.t + '</option>'; });
    productHtml += '</select><input type="text" name="items[' + idx + '][product_name_override]" class="return-name-override" style="margin-top:6px;display:none;" placeholder="Product name (when other)">';
    var storeHtml = '<select name="items[' + idx + '][store_id]" required><option value="">Select store</option>';
    storeOpts.forEach(function(o) { storeHtml += '<option value="' + o.v + '">' + o.t + '</option>'; });
    storeHtml += '</select>';
    tr.innerHTML = '<td class="dash-sale-item-product">' + productHtml + '</td><td class="dash-sale-item-store">' + storeHtml + '</td><td class="dash-sale-item-qty"><input type="number" name="items[' + idx + '][quantity]" min="0.01" step="any" required></td><td><input type="text" name="items[' + idx + '][reason]" placeholder="Optional"></td><td class="dash-sale-item-actions"><button type="button" class="dash-btn dash-btn-outline remove-return-item" style="padding:4px 8px;font-size:.75rem;">Remove</button></td>';
    tbody.appendChild(tr);
    tr.querySelector('.return-product').addEventListener('change', function() {
        var r = this.closest('tr');
        var o = r.querySelector('.return-name-override');
        if (this.value === '') { if (o) o.style.display = 'block'; } else { if (o) o.style.display = 'none'; }
    });
});
document.getElementById('return-items-tbody').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-return-item')) e.target.closest('tr').remove();
});
</script>
@endsection
