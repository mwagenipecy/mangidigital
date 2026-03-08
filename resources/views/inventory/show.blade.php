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
    <div style="padding:0 20px 20px;border-top:1px solid var(--dash-border);display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
        <form action="{{ route('inventory.update', $inventory) }}" method="POST" style="display:flex;align-items:flex-end;gap:8px;">
            @csrf
            @method('PATCH')
            <div>
                <label for="price_per_unit_edit" style="display:block;font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;">Update price (TZS)</label>
                <input type="number" id="price_per_unit_edit" name="price_per_unit" value="{{ $inventory->price_per_unit ?? $inventory->product->price ?? '' }}" min="0" step="1"
                    style="width:140px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
            <button type="submit" class="dash-btn dash-btn-outline">Update price</button>
        </form>
        @if(!$inventory->is_out_of_stock)
        <form action="{{ route('inventory.update', $inventory) }}" method="POST" style="display:inline;">
            @csrf
            @method('PATCH')
            <input type="hidden" name="is_out_of_stock" value="1">
            <button type="submit" class="dash-btn" style="background:var(--dash-danger);color:white;border:none;">Mark out of stock</button>
        </form>
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
    <div style="padding:0 20px 20px;display:flex;flex-wrap:wrap;gap:24px;">
        <form action="{{ route('inventory.add-stock', $inventory) }}" method="POST" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:10px;">
            @csrf
            <div>
                <label for="add_quantity" style="display:block;font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;">Quantity to add</label>
                <input type="number" id="add_quantity" name="quantity" value="{{ old('quantity') }}" min="0.01" step="any" required
                    style="width:100px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
            <div>
                <label for="add_reference" style="display:block;font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;">Reference</label>
                <input type="text" id="add_reference" name="reference" value="{{ old('reference', 'Restock') }}"
                    style="width:140px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
            <button type="submit" class="dash-btn dash-btn-brand">Add stock</button>
        </form>
        <form action="{{ route('inventory.remove-stock', $inventory) }}" method="POST" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:10px;">
            @csrf
            <div>
                <label for="remove_quantity" style="display:block;font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;">Quantity to remove</label>
                <input type="number" id="remove_quantity" name="quantity" value="{{ old('quantity') }}" min="0.01" step="any" max="{{ $inventory->quantity }}" required
                    style="width:100px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"
                    placeholder="Max {{ number_format($inventory->quantity, 0) }}">
            </div>
            <div>
                <label for="remove_reference" style="display:block;font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;">Reference</label>
                <input type="text" id="remove_reference" name="reference" value="{{ old('reference', 'Sale / Use') }}"
                    style="width:140px;padding:8px 10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
            <button type="submit" class="dash-btn" style="background:var(--dash-danger);color:white;border:none;">Remove stock</button>
        </form>
    </div>
</div>

{{-- Transactions --}}
<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Inventory transactions</div>
            <div class="dash-card-subtitle">From / to store, who added or removed, when</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Who</th>
                    <th>When</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventory->transactions as $tx)
                <tr>
                    <td>
                        @if($tx->type === \App\Models\InventoryTransaction::TYPE_IN)
                            <span class="dash-pill dash-pill-green">In</span>
                        @else
                            <span class="dash-pill dash-pill-red">Out</span>
                        @endif
                    </td>
                    <td><span class="dash-td-amount">{{ number_format($tx->quantity, 0) }} {{ $inventory->product->unit ?? '' }}</span></td>
                    <td><span class="dash-td-sub">{{ $tx->fromStore->name ?? '—' }}</span></td>
                    <td><span class="dash-td-sub">{{ $tx->toStore->name ?? '—' }}</span></td>
                    <td><span class="dash-td-main">{{ $tx->user->name ?? '—' }}</span></td>
                    <td><span class="dash-td-sub">{{ $tx->created_at?->format('d M Y H:i') ?? '—' }}</span></td>
                    <td><span class="dash-td-sub">{{ $tx->reference ?? '—' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:24px;color:var(--dash-muted);">No transactions yet. Add or remove stock to see history.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
