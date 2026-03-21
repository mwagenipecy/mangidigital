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
        <p class="dash-page-subtitle">Choose category → store → product from inventory; price from stock. Or use “Other” for a product not in catalog.</p>
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
                <div class="dash-card-subtitle">Pick an existing client to auto-fill name and phone, or enter a new client</div>
            </div>
        </div>
        <div class="dash-form-section">
            <div class="dash-form-grid dash-form-grid--4">
                <div class="dash-form-field">
                    <label for="client_id">Existing client (optional)</label>
                    <select id="client_id" name="client_id">
                        <option value="">— New client —</option>
                        @foreach($clients as $cl)
                            <option value="{{ $cl->id }}" data-name="{{ e($cl->name) }}" data-phone="{{ e($cl->phone ?? '') }}" {{ old('client_id') == $cl->id ? 'selected' : '' }}>{{ $cl->name }} — {{ $cl->phone }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="dash-form-field">
                    <label for="client_name">Client name</label>
                    <input type="text" id="client_name" name="client_name" value="{{ old('client_name') }}" placeholder="Name" autocomplete="off">
                </div>
                <div class="dash-form-field">
                    <label for="client_phone">Client phone</label>
                    <input type="text" id="client_phone" name="client_phone" value="{{ old('client_phone') }}" placeholder="Phone" autocomplete="off">
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
                <div class="dash-card-subtitle">Category → store → product (stock). Unit price comes from inventory; adjust if needed. Quantity × price = line total.</div>
            </div>
            <button type="button" id="add-sale-item" class="dash-btn dash-btn-outline">Add row</button>
        </div>
        <div class="dash-form-section">
            <div class="dash-sale-items-wrap" style="overflow-x:auto;">
                <table class="dash-table" id="sale-items-table" style="min-width:920px;">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Store</th>
                            <th>Product</th>
                            <th class="dash-sale-item-qty">Qty</th>
                            <th class="dash-sale-item-price">Unit price (TZS)</th>
                            <th>Line total</th>
                            <th class="dash-sale-item-actions"></th>
                        </tr>
                    </thead>
                    <tbody id="sale-items-tbody">
                        @include('sales.partials.sale-item-row', ['index' => 0, 'categories' => $categories, 'stores' => $stores])
                    </tbody>
                </table>
            </div>
            <div style="display:flex;justify-content:flex-end;margin-top:12px;gap:16px;align-items:center;">
                <span style="font-weight:600;color:var(--dash-ink);">Subtotal (items)</span>
                <span id="sale-items-subtotal" style="font-size:1.1rem;font-weight:700;">0 TZS</span>
            </div>
            <p class="dash-form-hint">“Other (not in catalog)” lets you sell without inventory; no stock is deducted.</p>
        </div>
    </div>

    <div class="dash-card dash-form-card">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">Delivery / logistics</div>
                <div class="dash-card-subtitle">Optional — choose local or international, filter by category, then pick a registered transport</div>
            </div>
        </div>
        <div class="dash-form-section">
            <label class="dash-form-check">
                <input type="checkbox" name="delivery_requested" value="1" {{ old('delivery_requested') ? 'checked' : '' }} id="delivery_requested">
                <span>Delivery requested</span>
            </label>
            <div id="delivery-fields" class="dash-form-section" style="{{ old('delivery_requested') ? '' : 'display:none;' }}margin-top:12px;">
                <div class="dash-form-grid dash-form-grid--2" style="margin-bottom:12px;">
                    <div class="dash-form-field">
                        <span style="display:block;font-size:.8rem;font-weight:600;margin-bottom:8px;">Delivery scope <span style="color:var(--dash-danger);">*</span> <span style="font-weight:400;color:var(--dash-muted);">(required if transport is selected)</span></span>
                        <div style="display:flex;gap:16px;flex-wrap:wrap;">
                            <label class="dash-form-check"><input type="radio" name="delivery_scope" value="local" class="js-delivery-scope" {{ old('delivery_scope') === 'local' ? 'checked' : '' }}> Local</label>
                            <label class="dash-form-check"><input type="radio" name="delivery_scope" value="international" class="js-delivery-scope" {{ old('delivery_scope') === 'international' ? 'checked' : '' }}> International</label>
                        </div>
                        @error('delivery_scope')<p class="dash-form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="dash-form-field">
                        <label for="delivery_category_filter_id">Filter transport by category</label>
                        <select id="delivery_category_filter_id" name="delivery_category_filter_id" class="js-delivery-category-filter">
                            <option value="">— All categories —</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ (string) old('delivery_category_filter_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="dash-form-grid dash-form-grid--2">
                    <div class="dash-form-field">
                        <label for="delivery_service_provider_id">Transport</label>
                        <select id="delivery_service_provider_id" name="delivery_service_provider_id" class="js-delivery-transport" data-old-provider="{{ old('delivery_service_provider_id') }}">
                            <option value="">— Select scope first —</option>
                        </select>
                        @error('delivery_service_provider_id')<p class="dash-form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="dash-form-field">
                        <label for="delivery_cost">Delivery cost (TZS)</label>
                        <input type="number" id="delivery_cost" name="delivery_cost" value="{{ old('delivery_cost') }}" min="0" step="1" placeholder="0">
                    </div>
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
(function() {
    const CATEGORY_OTHER = '__other__';
    const INV = @json($saleInventoryPayload);
    const STORES = @json($stores->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values());
    const PROVIDERS = @json($serviceProvidersPayload);
    const TYPE_LOCAL = @json(\App\Models\ServiceProvider::TYPE_LOCAL_TRANSPORT);
    const TYPE_INTL = @json(\App\Models\ServiceProvider::TYPE_INTERNATIONAL_TRANSPORT);
    const TYPE_CLEAR = @json(\App\Models\ServiceProvider::TYPE_CLEARANCE_FORWARDING);

    const clientSelect = document.getElementById('client_id');
    const clientName = document.getElementById('client_name');
    const clientPhone = document.getElementById('client_phone');
    if (clientSelect) {
        function fillClientFromSelect() {
            const opt = clientSelect.options[clientSelect.selectedIndex];
            if (!opt || !clientSelect.value) return;
            const n = opt.getAttribute('data-name');
            const p = opt.getAttribute('data-phone');
            if (n) clientName.value = n;
            if (p !== null) clientPhone.value = p;
        }
        clientSelect.addEventListener('change', fillClientFromSelect);
        if (clientSelect.value) fillClientFromSelect();
    }

    const deliveryCb = document.getElementById('delivery_requested');
    const deliveryFields = document.getElementById('delivery-fields');
    if (deliveryCb && deliveryFields) {
        deliveryCb.addEventListener('change', function() {
            deliveryFields.style.display = this.checked ? 'block' : 'none';
        });
    }

    function providerMatchesScope(p, scope) {
        if (scope === 'local') return p.type === TYPE_LOCAL;
        if (scope === 'international') return p.type === TYPE_INTL || p.type === TYPE_CLEAR;
        return false;
    }

    function providerMatchesCategoryFilter(p, filterId) {
        if (!filterId) return true;
        if (p.product_category_id === null || p.product_category_id === undefined) return true;
        return String(p.product_category_id) === String(filterId);
    }

    function rebuildTransportSelect() {
        const sel = document.getElementById('delivery_service_provider_id');
        const scopeEl = document.querySelector('.js-delivery-scope:checked');
        const filterEl = document.getElementById('delivery_category_filter_id');
        if (!sel) return;
        const prev = sel.value || sel.getAttribute('data-old-provider') || '';
        sel.innerHTML = '<option value="">— Select —</option>';
        const scope = scopeEl ? scopeEl.value : '';
        const filterId = filterEl ? filterEl.value : '';
        if (!scope) {
            sel.innerHTML = '<option value="">— Select local or international first —</option>';
            if (prev) sel.setAttribute('data-old-provider', prev);
            return;
        }
        PROVIDERS.forEach(function(p) {
            if (!providerMatchesScope(p, scope)) return;
            if (!providerMatchesCategoryFilter(p, filterId)) return;
            const o = document.createElement('option');
            o.value = p.id;
            o.textContent = p.name + ' (' + p.type_label + ')';
            if (String(p.id) === String(prev)) o.selected = true;
            sel.appendChild(o);
        });
        sel.removeAttribute('data-old-provider');
    }

    document.querySelectorAll('.js-delivery-scope').forEach(function(r) {
        r.addEventListener('change', rebuildTransportSelect);
    });
    const catFilter = document.getElementById('delivery_category_filter_id');
    if (catFilter) catFilter.addEventListener('change', rebuildTransportSelect);
    rebuildTransportSelect();

    function storesForCategory(categoryId) {
        const ids = new Set();
        INV.forEach(function(inv) {
            if (String(inv.category_id) === String(categoryId)) ids.add(String(inv.store_id));
        });
        return STORES.filter(function(s) { return ids.has(String(s.id)); });
    }

    function inventoryLinesFor(categoryId, storeId) {
        return INV.filter(function(inv) {
            return String(inv.category_id) === String(categoryId) && String(inv.store_id) === String(storeId);
        }).sort(function(a, b) {
            return (a.product_name || '').localeCompare(b.product_name || '');
        });
    }

    function getRowItemIndex(row) {
        const storeSel = row.querySelector('.js-item-store');
        const n = storeSel && storeSel.getAttribute('name');
        const m = n && n.match(/items\[(\d+)\]/);
        return m ? m[1] : '0';
    }

    function toggleRowOther(row, isOther) {
        const prodSel = row.querySelector('.js-item-product');
        const otherInp = row.querySelector('.js-other-name');
        const prodWrap = row.querySelector('.js-product-select-wrap');
        const otherWrap = row.querySelector('.js-other-name-wrap');
        if (!prodSel || !otherInp || !prodWrap || !otherWrap) return;
        let hiddenPid = row.querySelector('.js-hidden-empty-product-id');
        const idx = getRowItemIndex(row);

        if (isOther) {
            prodSel.disabled = true;
            prodSel.removeAttribute('name');
            prodSel.innerHTML = '';
            if (!hiddenPid) {
                hiddenPid = document.createElement('input');
                hiddenPid.type = 'hidden';
                hiddenPid.className = 'js-hidden-empty-product-id';
                hiddenPid.value = '';
                prodWrap.appendChild(hiddenPid);
            }
            hiddenPid.setAttribute('name', 'items[' + idx + '][product_id]');
            otherInp.disabled = false;
            otherInp.setAttribute('name', 'items[' + idx + '][product_name_override]');
            prodWrap.style.display = 'none';
            otherWrap.style.display = 'block';
        } else {
            if (hiddenPid) hiddenPid.remove();
            prodSel.disabled = false;
            prodSel.setAttribute('name', 'items[' + idx + '][product_id]');
            otherInp.value = '';
            otherInp.disabled = true;
            otherInp.removeAttribute('name');
            prodWrap.style.display = 'block';
            otherWrap.style.display = 'none';
        }
    }

    function getInventoryLine(categoryId, storeId, productId) {
        return INV.find(function(inv) {
            return String(inv.category_id) === String(categoryId)
                && String(inv.store_id) === String(storeId)
                && String(inv.product_id) === String(productId);
        });
    }

    function rowMode(row) {
        const cat = row.querySelector('.js-item-category');
        return cat && cat.value === CATEGORY_OTHER ? 'other' : 'catalog';
    }

    function refreshStoreOptions(row) {
        const cat = row.querySelector('.js-item-category');
        const storeSel = row.querySelector('.js-item-store');
        if (!cat || !storeSel) return;
        const v = cat.value;
        const prev = storeSel.value;
        storeSel.innerHTML = '<option value="">— Select store —</option>';
        if (!v || v === CATEGORY_OTHER) {
            STORES.forEach(function(s) {
                const o = document.createElement('option');
                o.value = s.id;
                o.textContent = s.name;
                storeSel.appendChild(o);
            });
            if (v === CATEGORY_OTHER && prev) storeSel.value = prev;
            return;
        }
        storesForCategory(v).forEach(function(s) {
            const o = document.createElement('option');
            o.value = s.id;
            o.textContent = s.name;
            storeSel.appendChild(o);
        });
        if (prev && Array.from(storeSel.options).some(function(o) { return o.value === prev; })) {
            storeSel.value = prev;
        }
    }

    function refreshProductOptions(row) {
        const cat = row.querySelector('.js-item-category');
        const storeSel = row.querySelector('.js-item-store');
        const prodSel = row.querySelector('.js-item-product');
        if (!cat || !prodSel) return;

        if (cat.value === CATEGORY_OTHER) {
            toggleRowOther(row, true);
            return;
        }
        toggleRowOther(row, false);

        const prev = prodSel.value;
        prodSel.innerHTML = '<option value="">— Select product —</option>';
        if (!cat.value || !storeSel || !storeSel.value) return;

        inventoryLinesFor(cat.value, storeSel.value).forEach(function(inv) {
            const o = document.createElement('option');
            o.value = inv.product_id;
            o.setAttribute('data-unit-price', inv.unit_price);
            o.setAttribute('data-qty-max', inv.qty_available);
            const unit = inv.unit ? ' ' + inv.unit : '';
            o.textContent = inv.product_name + unit + ' (' + inv.qty_available + ' avail.)';
            prodSel.appendChild(o);
        });
        if (prev && Array.from(prodSel.options).some(function(o) { return o.value === prev; })) {
            prodSel.value = prev;
        }
    }

    function applyPriceAndMax(row) {
        const cat = row.querySelector('.js-item-category');
        const storeSel = row.querySelector('.js-item-store');
        const prodSel = row.querySelector('.js-item-product');
        const priceInp = row.querySelector('.js-item-price');
        const qtyInp = row.querySelector('.js-item-qty');
        if (!priceInp || rowMode(row) !== 'catalog') return;
        const line = getInventoryLine(cat.value, storeSel.value, prodSel.value);
        if (line) {
            priceInp.value = Math.round(line.unit_price);
            if (qtyInp) {
                qtyInp.max = line.qty_available;
                qtyInp.setAttribute('title', 'Max ' + line.qty_available);
            }
        }
        updateLineTotal(row);
    }

    function updateLineTotal(row) {
        const qty = parseFloat(row.querySelector('.js-item-qty')?.value) || 0;
        const price = parseFloat(row.querySelector('.js-item-price')?.value) || 0;
        const el = row.querySelector('.js-item-line-total');
        if (el) el.textContent = Math.round(qty * price).toLocaleString() + ' TZS';
        updateSubtotal();
    }

    function updateSubtotal() {
        let sum = 0;
        document.querySelectorAll('#sale-items-tbody .sale-item-row').forEach(function(row) {
            const qty = parseFloat(row.querySelector('.js-item-qty')?.value) || 0;
            const price = parseFloat(row.querySelector('.js-item-price')?.value) || 0;
            sum += qty * price;
        });
        const el = document.getElementById('sale-items-subtotal');
        if (el) el.textContent = Math.round(sum).toLocaleString() + ' TZS';
    }

    function bindRow(row) {
        const cat = row.querySelector('.js-item-category');
        const storeSel = row.querySelector('.js-item-store');
        const prodSel = row.querySelector('.js-item-product');
        if (cat) {
            cat.addEventListener('change', function() {
                refreshStoreOptions(row);
                refreshProductOptions(row);
                if (rowMode(row) === 'other') {
                    const qtyInp = row.querySelector('.js-item-qty');
                    if (qtyInp) { qtyInp.removeAttribute('max'); qtyInp.removeAttribute('title'); }
                }
                applyPriceAndMax(row);
                updateLineTotal(row);
            });
        }
        if (storeSel) {
            storeSel.addEventListener('change', function() {
                refreshProductOptions(row);
                applyPriceAndMax(row);
            });
        }
        if (prodSel) {
            prodSel.addEventListener('change', function() {
                applyPriceAndMax(row);
            });
        }
        row.querySelectorAll('.js-item-qty, .js-item-price').forEach(function(inp) {
            inp.addEventListener('input', function() { updateLineTotal(row); });
        });
    }

    function reindexRows() {
        const rows = document.querySelectorAll('#sale-items-tbody .sale-item-row');
        rows.forEach(function(row, idx) {
            row.querySelectorAll('[name^="items["]').forEach(function(el) {
                const n = el.getAttribute('name');
                if (n) el.setAttribute('name', n.replace(/items\[\d+\]/, 'items[' + idx + ']'));
            });
        });
    }

    document.querySelectorAll('#sale-items-tbody .sale-item-row').forEach(function(row) {
        refreshStoreOptions(row);
        refreshProductOptions(row);
        bindRow(row);
        updateLineTotal(row);
    });

    document.getElementById('add-sale-item')?.addEventListener('click', function() {
        const tbody = document.getElementById('sale-items-tbody');
        const first = tbody.querySelector('.sale-item-row');
        if (!first) return;
        const clone = first.cloneNode(true);
        clone.querySelector('.js-hidden-empty-product-id')?.remove();
        clone.querySelectorAll('input, select').forEach(function(el) {
            if (el.type === 'checkbox' || el.type === 'radio') {
                el.checked = false;
            } else {
                el.value = '';
            }
        });
        const prodSel = clone.querySelector('.js-item-product');
        if (prodSel) {
            prodSel.innerHTML = '<option value="">— Select product —</option>';
            prodSel.disabled = false;
            prodSel.removeAttribute('disabled');
        }
        const cat = clone.querySelector('.js-item-category');
        if (cat) cat.value = '';
        const otherInp = clone.querySelector('.js-other-name');
        if (otherInp) {
            otherInp.disabled = true;
            otherInp.removeAttribute('name');
        }
        clone.querySelector('.js-product-select-wrap')?.style && (clone.querySelector('.js-product-select-wrap').style.display = 'block');
        clone.querySelector('.js-other-name-wrap')?.style && (clone.querySelector('.js-other-name-wrap').style.display = 'none');
        tbody.appendChild(clone);
        reindexRows();
        refreshStoreOptions(clone);
        refreshProductOptions(clone);
        bindRow(clone);
        updateLineTotal(clone);
    });

    document.getElementById('sale-items-tbody')?.addEventListener('click', function(e) {
        if (e.target.classList.contains('js-remove-sale-item')) {
            const tr = e.target.closest('tr');
            if (tr && document.querySelectorAll('#sale-items-tbody .sale-item-row').length > 1) {
                tr.remove();
                reindexRows();
                updateSubtotal();
            }
        }
    });

    document.querySelectorAll('.js-remove-sale-item').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const tr = btn.closest('tr');
            if (tr && document.querySelectorAll('#sale-items-tbody .sale-item-row').length > 1) {
                tr.remove();
                reindexRows();
                updateSubtotal();
            }
        });
    });
})();
</script>
@endsection
