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
    <div class="dash-card" style="margin-bottom:20px;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">Client</div>
                <div class="dash-card-subtitle">Select existing or enter name and phone (stored in clients)</div>
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;">
                <div>
                    <label for="client_id" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Existing client (optional)</label>
                    <select id="client_id" name="client_id" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                        <option value="">— New client —</option>
                        @foreach(\App\Models\Client::where('organization_id', auth()->user()->organization?->id)->orderBy('name')->get() as $cl)
                            <option value="{{ $cl->id }}" {{ old('client_id') == $cl->id ? 'selected' : '' }}>{{ $cl->name }} — {{ $cl->phone }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="client_name" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Client name</label>
                    <input type="text" id="client_name" name="client_name" value="{{ old('client_name') }}" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;" placeholder="If new client">
                </div>
                <div>
                    <label for="client_phone" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Client phone</label>
                    <input type="text" id="client_phone" name="client_phone" value="{{ old('client_phone') }}" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;" placeholder="Required for new client">
                </div>
                <div>
                    <label for="sale_date" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Sale date *</label>
                    <input type="date" id="sale_date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" required style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                </div>
            </div>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:20px;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">Sale items</div>
                <div class="dash-card-subtitle">Product (or “Other” for product not in system), store, quantity, unit price — write price for discount</div>
            </div>
            <button type="button" id="add-sale-item" class="dash-btn dash-btn-outline" style="font-size:.85rem;">Add row</button>
        </div>
        <div style="padding:0 20px 20px;overflow-x:auto;">
            <table class="dash-table" id="sale-items-table">
                <thead>
                    <tr>
                        <th>Product / name</th>
                        <th>Store</th>
                        <th>Qty</th>
                        <th>Unit price (TZS)</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="sale-items-tbody">
                    <tr class="sale-item-row">
                        <td>
                            <select name="items[0][product_id]" class="item-product" style="width:100%;min-width:160px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                                <option value="">— Other (not in system) —</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" data-price="{{ $p->price }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="items[0][product_name_override]" class="item-name-override" style="width:100%;min-width:140px;margin-top:4px;padding:6px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.85rem;display:none;" placeholder="Product name (when other)">
                        </td>
                        <td>
                            <select name="items[0][store_id]" style="width:100%;min-width:120px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                                <option value="">—</option>
                                @foreach($stores as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="items[0][quantity]" value="{{ old('items.0.quantity') }}" min="0.01" step="any" required style="width:80px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"></td>
                        <td><input type="number" name="items[0][unit_price]" class="item-unit-price" value="{{ old('items.0.unit_price') }}" min="0" step="1" required style="width:110px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;" placeholder="Price / discount"></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <p style="font-size:.8rem;color:var(--dash-muted);margin-top:8px;">Use “Other” to sell a product not in the system; enter product name in the field below. Unit price can be edited for discounts.</p>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:20px;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">Delivery / logistics</div>
                <div class="dash-card-subtitle">If client requested delivery — select transport and cost</div>
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            <label style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                <input type="checkbox" name="delivery_requested" value="1" {{ old('delivery_requested') ? 'checked' : '' }} id="delivery_requested" style="width:18px;height:18px;">
                <span>Delivery requested</span>
            </label>
            <div id="delivery-fields" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;{{ old('delivery_requested') ? '' : 'display:none;' }}">
                <div>
                    <label for="delivery_service_provider_id" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Transport</label>
                    <select id="delivery_service_provider_id" name="delivery_service_provider_id" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                        <option value="">— Select —</option>
                        @foreach($serviceProviders as $sp)
                            <option value="{{ $sp->id }}" {{ old('delivery_service_provider_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }} ({{ $sp->type_label }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="delivery_cost" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Delivery cost (TZS)</label>
                    <input type="number" id="delivery_cost" name="delivery_cost" value="{{ old('delivery_cost') }}" min="0" step="1" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;" placeholder="0">
                </div>
            </div>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:20px;">
        <div style="padding:20px;">
            <label for="notes" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Notes</label>
            <textarea id="notes" name="notes" rows="2" style="width:100%;max-width:500px;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">{{ old('notes') }}</textarea>
            <button type="submit" class="dash-btn dash-btn-brand" style="margin-top:16px;">Record sale &amp; get receipt</button>
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
    var productHtml = '<select name="items[' + idx + '][product_id]" class="item-product" style="width:100%;min-width:160px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"><option value="">— Other (not in system) —</option>';
    productOpts.filter(function(o) { return o.value; }).forEach(function(o) {
        productHtml += '<option value="' + o.value + '" data-price="' + (o.price || '') + '">' + o.text + '</option>';
    });
    productHtml += '</select><input type="text" name="items[' + idx + '][product_name_override]" class="item-name-override" style="width:100%;min-width:140px;margin-top:4px;padding:6px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.85rem;display:none;" placeholder="Product name (when other)">';
    var storeHtml = '<select name="items[' + idx + '][store_id]" style="width:100%;min-width:120px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"><option value="">—</option>';
    storeOpts.filter(function(o) { return o.value; }).forEach(function(o) {
        storeHtml += '<option value="' + o.value + '">' + o.text + '</option>';
    });
    storeHtml += '</select>';
    tr.innerHTML = '<td>' + productHtml + '</td><td>' + storeHtml + '</td><td><input type="number" name="items[' + idx + '][quantity]" min="0.01" step="any" required style="width:80px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"></td><td><input type="number" name="items[' + idx + '][unit_price]" class="item-unit-price" min="0" step="1" required style="width:110px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"></td><td><button type="button" class="dash-btn dash-btn-outline remove-sale-item" style="padding:4px 8px;font-size:.75rem;">Remove</button></td>';
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
