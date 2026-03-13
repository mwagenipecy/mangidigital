@extends('layouts.dashboard')

@section('title', 'New sale')

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('sales.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            Back to sales
        </a>
        <h1 class="dash-page-title">New sale</h1>
        <p class="dash-page-subtitle">Select product and store, or sell product not in system — bulk price and delivery</p>
    </div>
</div>

@if($errors->any())
    <div class="dash-card" style="margin-bottom:16px;background:rgba(239,68,68,.08);border-color:var(--dash-danger);">
        <ul style="margin:0;padding-left:18px;font-size:.9rem;color:var(--dash-danger);">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form action="{{ route('sales.store') }}" method="POST" id="sale-form">
    @csrf

    <div class="dash-card dash-form-card">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">Client</div>
                <div class="dash-card-subtitle">Select existing client or enter name and phone for a new client</div>
            </div>
        </div>
        <div class="dash-form-section">
            <div class="dash-form-grid dash-form-grid--4">
                <div class="dash-form-field">
                    <label for="client_id">Existing client (optional)</label>
                    <select id="client_id" name="client_id">
                        <option value="">— New client —</option>
                        @foreach(\App\Models\Client::where('organization_id', auth()->user()->organization?->id)->orderBy('name')->get() as $cl)
                            <option value="{{ $cl->id }}" {{ old('client_id') == $cl->id ? 'selected' : '' }}>{{ $cl->name }} — {{ $cl->phone }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="dash-form-field">
                    <label for="client_name">Client name</label>
                    <input type="text" id="client_name" name="client_name" value="{{ old('client_name') }}" placeholder="If new client">
                </div>
                <div class="dash-form-field">
                    <label for="client_phone">Client phone</label>
                    <input type="text" id="client_phone" name="client_phone" value="{{ old('client_phone') }}" placeholder="Required for new client">
                </div>
                <div class="dash-form-field">
                    <label for="sale_date">Sale date <span style="color:var(--dash-danger);">*</span></label>
                    <input type="date" id="sale_date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" required>
                </div>
            </div>
        </div>
    </div>

    <div class="dash-card dash-form-card">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">Sale items</div>
                <div class="dash-card-subtitle">Product (or “Other” for product not in system), store, quantity and unit price</div>
            </div>
            <button type="button" id="add-sale-item" class="dash-btn dash-btn-outline">Add row</button>
        </div>
        <div class="dash-form-section">
            <div class="dash-sale-items-wrap">
                <table class="dash-table" id="sale-items-table">
                    <thead>
                        <tr>
                            <th class="dash-sale-item-product">Product / name</th>
                            <th class="dash-sale-item-store">Store</th>
                            <th class="dash-sale-item-qty">Qty</th>
                            <th class="dash-sale-item-price">Unit price (TZS)</th>
                            <th class="dash-sale-item-actions"></th>
                        </tr>
                    </thead>
                    <tbody id="sale-items-tbody">
                        <tr class="sale-item-row">
                            <td class="dash-sale-item-product">
                                <select name="items[0][product_id]" class="item-product">
                                    <option value="">— Other (not in system) —</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" data-price="{{ $p->price }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="items[0][product_name_override]" class="item-name-override" style="margin-top:6px;display:none;" placeholder="Product name (when other)">
                            </td>
                            <td class="dash-sale-item-store">
                                <select name="items[0][store_id]">
                                    <option value="">—</option>
                                    @foreach($stores as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="dash-sale-item-qty"><input type="number" name="items[0][quantity]" value="{{ old('items.0.quantity') }}" min="0.01" step="any" required></td>
                            <td class="dash-sale-item-price"><input type="number" name="items[0][unit_price]" class="item-unit-price" value="{{ old('items.0.unit_price') }}" min="0" step="1" required placeholder="Price"></td>
                            <td class="dash-sale-item-actions"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="dash-form-hint">Use “Other” to sell a product not in the system; enter the product name in the field that appears. Unit price can be edited for discounts.</p>
        </div>
    </div>

    <div class="dash-card dash-form-card">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">Delivery / logistics</div>
                <div class="dash-card-subtitle">Optional — select transport and cost if client requested delivery</div>
            </div>
        </div>
        <div class="dash-form-section">
            <label class="dash-form-check">
                <input type="checkbox" name="delivery_requested" value="1" {{ old('delivery_requested') ? 'checked' : '' }} id="delivery_requested">
                <span>Delivery requested</span>
            </label>
            <div id="delivery-fields" class="dash-form-grid dash-form-grid--2" style="{{ old('delivery_requested') ? '' : 'display:none;' }}">
                <div class="dash-form-field">
                    <label for="delivery_service_provider_id">Transport</label>
                    <select id="delivery_service_provider_id" name="delivery_service_provider_id">
                        <option value="">— Select —</option>
                        @foreach($serviceProviders as $sp)
                            <option value="{{ $sp->id }}" {{ old('delivery_service_provider_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }} ({{ $sp->type_label }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="dash-form-field">
                    <label for="delivery_cost">Delivery cost (TZS)</label>
                    <input type="number" id="delivery_cost" name="delivery_cost" value="{{ old('delivery_cost') }}" min="0" step="1" placeholder="0">
                </div>
            </div>
        </div>
    </div>

    <div class="dash-card dash-form-card">
        <div class="dash-form-section">
            <div class="dash-form-field" style="max-width:100%;">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Optional notes for this sale">{{ old('notes') }}</textarea>
            </div>
            <div class="dash-form-actions">
                <button type="submit" class="dash-btn dash-btn-brand">
                    <flux:icon.check class="size-4" />
                    Record sale &amp; get receipt
                </button>
            </div>
        </div>
    </div>
</form>

<script>
document.getElementById('delivery_requested').addEventListener('change', function() {
    document.getElementById('delivery-fields').style.display = this.checked ? 'grid' : 'none';
});
document.querySelectorAll('.item-product').forEach(function(sel) {
    sel.addEventListener('change', function() {
        var row = this.closest('tr');
        var override = row.querySelector('.item-name-override');
        var priceInp = row.querySelector('.item-unit-price');
        if (this.value === '') {
            if (override) override.style.display = 'block';
        } else {
            if (override) override.style.display = 'none';
            var opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.price && !priceInp.value) priceInp.value = opt.dataset.price;
        }
    });
});
document.getElementById('add-sale-item').addEventListener('click', function() {
    var tbody = document.getElementById('sale-items-tbody');
    var idx = tbody.querySelectorAll('.sale-item-row').length;
    var firstRow = tbody.querySelector('.sale-item-row');
    var productSelect = firstRow.querySelector('.item-product');
    var storeSelect = firstRow.querySelector('select[name^="items"][name*="store_id"]');
    var productOpts = Array.from(productSelect.options).map(function(o) {
        return { value: o.value, text: o.text, price: o.dataset.price || '' };
    });
    var storeOpts = Array.from(storeSelect.options).map(function(o) {
        return { value: o.value, text: o.text };
    });
    var tr = document.createElement('tr');
    tr.className = 'sale-item-row';
    var productHtml = '<select name="items[' + idx + '][product_id]" class="item-product"><option value="">— Other (not in system) —</option>';
    productOpts.filter(function(o) { return o.value; }).forEach(function(o) {
        productHtml += '<option value="' + o.value + '" data-price="' + (o.price || '') + '">' + o.text + '</option>';
    });
    productHtml += '</select><input type="text" name="items[' + idx + '][product_name_override]" class="item-name-override" style="margin-top:6px;display:none;" placeholder="Product name (when other)">';
    var storeHtml = '<select name="items[' + idx + '][store_id]"><option value="">—</option>';
    storeOpts.filter(function(o) { return o.value; }).forEach(function(o) {
        storeHtml += '<option value="' + o.value + '">' + o.text + '</option>';
    });
    storeHtml += '</select>';
    tr.innerHTML = '<td class="dash-sale-item-product">' + productHtml + '</td><td class="dash-sale-item-store">' + storeHtml + '</td><td class="dash-sale-item-qty"><input type="number" name="items[' + idx + '][quantity]" min="0.01" step="any" required></td><td class="dash-sale-item-price"><input type="number" name="items[' + idx + '][unit_price]" class="item-unit-price" min="0" step="1" required placeholder="Price"></td><td class="dash-sale-item-actions"><button type="button" class="dash-btn dash-btn-outline remove-sale-item" style="padding:4px 8px;font-size:.75rem;">Remove</button></td>';
    tbody.appendChild(tr);
    tr.querySelector('.item-product').addEventListener('change', function() {
        var r = this.closest('tr');
        var ov = r.querySelector('.item-name-override');
        var pr = r.querySelector('.item-unit-price');
        if (this.value === '') { if (ov) ov.style.display = 'block'; } else {
            if (ov) ov.style.display = 'none';
            var o = this.options[this.selectedIndex];
            if (o && o.dataset.price && !pr.value) pr.value = o.dataset.price;
        }
    });
    tr.querySelector('.remove-sale-item').addEventListener('click', function() { tr.remove(); });
});
document.getElementById('sale-items-tbody').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-sale-item')) e.target.closest('tr').remove();
});
</script>
@endsection
