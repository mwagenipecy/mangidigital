<tr class="sale-item-row">
    <td>
        <select class="js-item-category" autocomplete="off">
            <option value="">— Category —</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
            <option value="__other__">Other (not in catalog)</option>
        </select>
    </td>
    <td>
        <select class="js-item-store" name="items[{{ $index }}][store_id]">
            <option value="">— Select store —</option>
            @foreach($stores as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <div class="js-product-select-wrap">
            <select class="js-item-product" name="items[{{ $index }}][product_id]">
                <option value="">— Select product —</option>
            </select>
        </div>
        <div class="js-other-name-wrap" style="display:none;margin-top:6px;">
            <input type="text" class="js-other-name" placeholder="Product name" autocomplete="off" disabled>
        </div>
    </td>
    <td class="dash-sale-item-qty">
        <input type="number" class="js-item-qty" name="items[{{ $index }}][quantity]" min="0.01" step="any" required>
    </td>
    <td class="dash-sale-item-price">
        <input type="number" class="js-item-price" name="items[{{ $index }}][unit_price]" min="0" step="1" required placeholder="Price">
    </td>
    <td><span class="js-item-line-total" style="font-weight:600;white-space:nowrap;">0 TZS</span></td>
    <td class="dash-sale-item-actions">
        <button type="button" class="dash-btn dash-btn-outline js-remove-sale-item" style="padding:4px 8px;font-size:.75rem;">Remove</button>
    </td>
</tr>
