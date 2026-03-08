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
    <div class="dash-card" style="margin-bottom:20px;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">Return details</div>
                <div class="dash-card-subtitle">Optional: link to a sale</div>
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;">
                <div>
                    <label for="sale_id" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Sale (optional)</label>
                    <select id="sale_id" name="sale_id" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                        <option value="">— None —</option>
                        @foreach($sales as $s)
                            <option value="{{ $s->id }}" {{ old('sale_id') == $s->id ? 'selected' : '' }}>{{ $s->receipt_number ?? '#' . $s->id }} — {{ $s->sale_date?->format('d M Y') }} ({{ $s->display_client_name }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="return_date" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Return date *</label>
                    <input type="date" id="return_date" name="return_date" value="{{ old('return_date', date('Y-m-d')) }}" required style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                </div>
            </div>
            <div style="margin-top:16px;">
                <label for="notes" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Notes</label>
                <textarea id="notes" name="notes" rows="2" style="width:100%;max-width:400px;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:20px;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">Items returned</div>
                <div class="dash-card-subtitle">Product, store to return to, quantity — stock will be added back to inventory</div>
            </div>
            <button type="button" id="add-return-item" class="dash-btn dash-btn-outline" style="font-size:.85rem;">Add row</button>
        </div>
        <div style="padding:0 20px 20px;overflow-x:auto;">
            <table class="dash-table" id="return-items-table">
                <thead>
                    <tr>
                        <th>Product / name</th>
                        <th>Store (return to)</th>
                        <th>Quantity</th>
                        <th>Reason</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="return-items-tbody">
                    <tr class="return-item-row">
                        <td>
                            <select name="items[0][product_id]" class="return-product" style="width:100%;min-width:160px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                                <option value="">— Other —</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="items[0][product_name_override]" class="return-name-override" style="width:100%;min-width:140px;margin-top:4px;padding:6px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.85rem;display:none;" placeholder="Product name (when other)">
                        </td>
                        <td>
                            <select name="items[0][store_id]" required style="width:100%;min-width:120px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                                <option value="">Select store</option>
                                @foreach($stores as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="items[0][quantity]" value="{{ old('items.0.quantity') }}" min="0.01" step="any" required style="width:90px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"></td>
                        <td><input type="text" name="items[0][reason]" value="{{ old('items.0.reason') }}" style="width:140px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;" placeholder="Optional"></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p style="font-size:.8rem;color:var(--dash-muted);margin:8px 20px 20px;">Only items with a product from your catalog will update inventory. “Other” is for record only.</p>
        <div style="padding:0 20px 20px;">
            <button type="submit" class="dash-btn dash-btn-brand">Save return</button>
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
    var productHtml = '<select name="items[' + idx + '][product_id]" class="return-product" style="width:100%;min-width:160px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"><option value="">— Other —</option>';
    productOpts.forEach(function(o) { productHtml += '<option value="' + o.v + '">' + o.t + '</option>'; });
    productHtml += '</select><input type="text" name="items[' + idx + '][product_name_override]" class="return-name-override" style="width:100%;min-width:140px;margin-top:4px;padding:6px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.85rem;display:none;" placeholder="Product name (when other)">';
    var storeHtml = '<select name="items[' + idx + '][store_id]" required style="width:100%;min-width:120px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"><option value="">Select store</option>';
    storeOpts.forEach(function(o) { storeHtml += '<option value="' + o.v + '">' + o.t + '</option>'; });
    storeHtml += '</select>';
    tr.innerHTML = '<td>' + productHtml + '</td><td>' + storeHtml + '</td><td><input type="number" name="items[' + idx + '][quantity]" min="0.01" step="any" required style="width:90px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"></td><td><input type="text" name="items[' + idx + '][reason]" style="width:140px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"></td><td><button type="button" class="dash-btn dash-btn-outline remove-return-item" style="padding:4px 8px;font-size:.75rem;">Remove</button></td>';
    tbody.appendChild(tr);
    tr.querySelector('.return-product').addEventListener('change', function() {
        var r = this.closest('tr');
        var o = r.querySelector('.return-name-override');
        if (this.value === '') { if (o) o.style.display = 'block'; } else { if (o) o.style.display = 'none'; }
    });
    tr.querySelector('.remove-return-item').addEventListener('click', function() { tr.remove(); });
});
document.getElementById('return-items-tbody').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-return-item')) e.target.closest('tr').remove();
});
</script>
@endsection
