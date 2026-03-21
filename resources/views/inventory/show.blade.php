@extends('layouts.dashboard')

@section('title', __('Inventory') . ' — ' . ($inventory->product->name ?? '—'))

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('inventory.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            Back to inventory
        </a>
        <h1 class="dash-page-title">{{ $inventory->product->name ?? '—' }}</h1>
        <p class="dash-page-subtitle">{{ $inventory->store->name ?? '—' }} — view transactions, update price, add/remove stock</p>
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

{{-- Summary card --}}
<div class="dash-card" style="margin-bottom:20px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Current stock</div>
            <div class="dash-card-subtitle">Product · Store · Quantity · Price</div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            @if($inventory->is_out_of_stock)
                <span class="dash-pill dash-pill-red">Out of stock</span>
            @else
                <span class="dash-pill dash-pill-green">In stock</span>
            @endif
        </div>
    </div>
    <div style="padding:0 20px 20px;display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:16px;">
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:2px;">Product</div>
            <div style="font-weight:600;">{{ $inventory->product->name ?? '—' }}</div>
        </div>
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:2px;">Store</div>
            <div style="font-weight:600;">{{ $inventory->store->name ?? '—' }}</div>
        </div>
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:2px;">Quantity</div>
            <div style="font-weight:600;">{{ number_format($inventory->quantity, 0) }} {{ $inventory->product->unit ?? '' }}</div>
        </div>
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:2px;">Price per unit (TZS)</div>
            <div style="font-weight:600;">{{ $inventory->display_price ?? '—' }}</div>
        </div>
    </div>
    <div style="padding:0 20px 20px;border-top:1px solid var(--dash-border);display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
        <button type="button" class="dash-btn dash-btn-outline" onclick="document.getElementById('modalPrice').classList.add('show')">Update price</button>
        @if(!$inventory->is_out_of_stock)
        <button type="button" class="dash-btn" style="background:var(--dash-danger);color:white;border:none;" onclick="document.getElementById('modalOutStock').classList.add('show')">Mark out of stock</button>
        @endif
    </div>
</div>

{{-- Add / Remove stock --}}
<div class="dash-card" style="margin-bottom:20px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Adjust stock</div>
            <div class="dash-card-subtitle">Add or remove quantity — creates a transaction</div>
        </div>
    </div>
    <div style="padding:0 20px 20px;display:flex;flex-wrap:wrap;gap:10px;">
        <button type="button" class="dash-btn dash-btn-brand" onclick="document.getElementById('modalAddStock').classList.add('show')">Add stock</button>
        <button type="button" class="dash-btn" style="background:var(--dash-danger);color:white;border:none;" onclick="document.getElementById('modalRemoveStock').classList.add('show')">Remove stock</button>
    </div>
</div>

{{-- Modal: Update price --}}
<div class="dash-modal-overlay" id="modalPrice" role="dialog" aria-modal="true" aria-labelledby="modalPriceTitle" onclick="if(event.target===this) this.classList.remove('show')">
    <div class="dash-modal-dialog" onclick="event.stopPropagation()">
        <div class="dash-modal-header">
            <h2 class="dash-modal-title" id="modalPriceTitle">Update price</h2>
            <button type="button" class="dash-modal-close" onclick="document.getElementById('modalPrice').classList.remove('show')" aria-label="Close">&times;</button>
        </div>
        <div class="dash-modal-body">
            <form action="{{ route('inventory.update', $inventory) }}" method="POST" class="js-loading-form">
                @csrf
                @method('PATCH')
                <div style="margin-bottom:12px;">
                    <label for="price_per_unit_modal" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Price per unit (TZS)</label>
                    <input type="number" id="price_per_unit_modal" name="price_per_unit" value="{{ $inventory->price_per_unit ?? $inventory->product->price ?? '' }}" min="0" step="1"
                        style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;box-sizing:border-box;">
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" class="dash-btn dash-btn-outline" onclick="document.getElementById('modalPrice').classList.remove('show')">Cancel</button>
                    <button type="submit" class="dash-btn dash-btn-brand js-loading-btn" data-loading-text="Saving...">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Mark out of stock --}}
<div class="dash-modal-overlay" id="modalOutStock" role="dialog" aria-modal="true" aria-labelledby="modalOutStockTitle" onclick="if(event.target===this) this.classList.remove('show')">
    <div class="dash-modal-dialog" onclick="event.stopPropagation()">
        <div class="dash-modal-header">
            <h2 class="dash-modal-title" id="modalOutStockTitle">Mark out of stock</h2>
            <button type="button" class="dash-modal-close" onclick="document.getElementById('modalOutStock').classList.remove('show')" aria-label="Close">&times;</button>
        </div>
        <div class="dash-modal-body">
            <p style="margin:0 0 14px;font-size:.9rem;color:var(--dash-text);">This will set quantity to zero and mark this line as out of stock.</p>
            <form action="{{ route('inventory.update', $inventory) }}" method="POST" class="js-loading-form">
                @csrf
                @method('PATCH')
                <input type="hidden" name="is_out_of_stock" value="1">
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" class="dash-btn dash-btn-outline" onclick="document.getElementById('modalOutStock').classList.remove('show')">Cancel</button>
                    <button type="submit" class="dash-btn js-loading-btn" style="background:var(--dash-danger);color:white;border:none;" data-loading-text="Updating...">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Add stock --}}
<div class="dash-modal-overlay" id="modalAddStock" role="dialog" aria-modal="true" aria-labelledby="modalAddStockTitle" onclick="if(event.target===this) this.classList.remove('show')">
    <div class="dash-modal-dialog" onclick="event.stopPropagation()">
        <div class="dash-modal-header">
            <h2 class="dash-modal-title" id="modalAddStockTitle">Add stock</h2>
            <button type="button" class="dash-modal-close" onclick="document.getElementById('modalAddStock').classList.remove('show')" aria-label="Close">&times;</button>
        </div>
        <div class="dash-modal-body">
            <form action="{{ route('inventory.add-stock', $inventory) }}" method="POST" class="js-loading-form">
                @csrf
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div>
                        <label for="add_quantity_modal" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Quantity to add *</label>
                        <input type="number" id="add_quantity_modal" name="quantity" value="{{ old('quantity') }}" min="0.01" step="any" required
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;box-sizing:border-box;">
                    </div>
                    <div>
                        <label for="add_reference_modal" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Reference</label>
                        <input type="text" id="add_reference_modal" name="reference" value="{{ old('reference', 'Restock') }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;box-sizing:border-box;">
                    </div>
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" class="dash-btn dash-btn-outline" onclick="document.getElementById('modalAddStock').classList.remove('show')">Cancel</button>
                        <button type="submit" class="dash-btn dash-btn-brand js-loading-btn" data-loading-text="Adding...">Add stock</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Remove stock --}}
<div class="dash-modal-overlay" id="modalRemoveStock" role="dialog" aria-modal="true" aria-labelledby="modalRemoveStockTitle" onclick="if(event.target===this) this.classList.remove('show')">
    <div class="dash-modal-dialog" onclick="event.stopPropagation()">
        <div class="dash-modal-header">
            <h2 class="dash-modal-title" id="modalRemoveStockTitle">Remove stock</h2>
            <button type="button" class="dash-modal-close" onclick="document.getElementById('modalRemoveStock').classList.remove('show')" aria-label="Close">&times;</button>
        </div>
        <div class="dash-modal-body">
            <form action="{{ route('inventory.remove-stock', $inventory) }}" method="POST" class="js-loading-form">
                @csrf
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div>
                        <label for="remove_quantity_modal" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Quantity to remove *</label>
                        <input type="number" id="remove_quantity_modal" name="quantity" value="{{ old('quantity') }}" min="0.01" step="any" max="{{ $inventory->quantity }}" required
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;box-sizing:border-box;"
                            placeholder="Max {{ number_format($inventory->quantity, 0) }}">
                    </div>
                    <div>
                        <label for="remove_reference_modal" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Reference</label>
                        <input type="text" id="remove_reference_modal" name="reference" value="{{ old('reference', 'Sale / Use') }}"
                            style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;box-sizing:border-box;">
                    </div>
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" class="dash-btn dash-btn-outline" onclick="document.getElementById('modalRemoveStock').classList.remove('show')">Cancel</button>
                        <button type="submit" class="dash-btn js-loading-btn" style="background:var(--dash-danger);color:white;border:none;" data-loading-text="Removing...">Remove stock</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Transactions --}}
<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Inventory transactions</div>
            <div class="dash-card-subtitle">Newest first — balance is stock on hand after that transaction (chronological); top row = last movement</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Balance</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Who</th>
                    <th>When</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <td>
                        @if($tx->type === \App\Models\InventoryTransaction::TYPE_IN)
                            <span class="dash-pill dash-pill-green">In</span>
                        @else
                            <span class="dash-pill dash-pill-red">Out</span>
                        @endif
                    </td>
                    <td><span class="dash-td-amount">{{ number_format($tx->quantity, 0) }} {{ $inventory->product->unit ?? '' }}</span></td>
                    <td><span class="dash-td-amount">{{ number_format($balanceAfterByTransactionId[$tx->id] ?? 0, 0) }} {{ $inventory->product->unit ?? '' }}</span></td>
                    <td><span class="dash-td-sub">{{ $tx->fromStore->name ?? '—' }}</span></td>
                    <td><span class="dash-td-sub">{{ $tx->toStore->name ?? '—' }}</span></td>
                    <td><span class="dash-td-main">{{ $tx->user->name ?? '—' }}</span></td>
                    <td><span class="dash-td-sub">{{ $tx->created_at?->format('d M Y H:i') ?? '—' }}</span></td>
                    <td><span class="dash-td-sub">{{ $tx->reference ?? '—' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:24px;color:var(--dash-muted);">No transactions yet. Add or remove stock to see history.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('submit', function (event) {
    const form = event.target.closest('.js-loading-form');
    if (!form) return;
    const buttons = form.querySelectorAll('.js-loading-btn');
    buttons.forEach((button) => {
        if (button.disabled) return;
        button.dataset.originalText = button.textContent.trim();
        button.disabled = true;
        button.textContent = button.dataset.loadingText || 'Please wait...';
    });
});
</script>
@endpush
