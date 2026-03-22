@extends('layouts.dashboard')

@section('title', __('Record inventory'))

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('inventory.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            Back to inventory
        </a>
        <h1 class="dash-page-title">Record inventory</h1>
        <p class="dash-page-subtitle">Select product, store, quantity and price</p>
    </div>
</div>

@if($products->isEmpty() || $stores->isEmpty())
    <div class="dash-card" style="background:var(--dash-brand-10);border-color:var(--dash-brand);">
        <p style="margin:0;font-size:.9rem;color:var(--dash-ink);">
            @if($products->isEmpty())
                Add at least one <a href="{{ route('products.index') }}" class="text-[var(--dash-brand)] font-semibold" wire:navigate>product</a> first.
            @endif
            @if($stores->isEmpty())
                Add at least one <a href="{{ route('stores.create') }}" class="text-[var(--dash-brand)] font-semibold" wire:navigate>store</a> first.
            @endif
        </p>
    </div>
@else
<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">New inventory entry</div>
            <div class="dash-card-subtitle">Product, store, quantity and price per unit</div>
        </div>
    </div>
    <form action="{{ route('inventory.store') }}" method="POST" style="padding:0 20px 20px;">
        @csrf
        <div style="display:flex;flex-direction:column;gap:16px;max-width:400px;">
            <div>
                <label for="product_id" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Product <span style="color:var(--dash-danger);">*</span></label>
                <select id="product_id" name="product_id" required
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                    <option value="">Select product</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ number_format($p->price, 0) }} TZS)</option>
                    @endforeach
                </select>
                @error('product_id')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="store_id" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Store / shop <span style="color:var(--dash-danger);">*</span></label>
                <select id="store_id" name="store_id" required
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                    <option value="">Select store</option>
                    @foreach($stores as $s)
                        <option value="{{ $s->id }}" {{ old('store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
                @error('store_id')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="quantity" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Quantity <span style="color:var(--dash-danger);">*</span></label>
                <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" min="0" step="1" required
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"
                    placeholder="0">
                @error('quantity')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="buying_price_per_unit" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Buying price per unit (TZS) <span style="font-weight:400;color:var(--dash-muted);">— cost</span></label>
                <input type="number" id="buying_price_per_unit" name="buying_price_per_unit" value="{{ old('buying_price_per_unit') }}" min="0" step="1"
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"
                    placeholder="How much you paid per unit">
                @error('buying_price_per_unit')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="price_per_unit" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Selling price per unit (TZS)</label>
                <input type="number" id="price_per_unit" name="price_per_unit" value="{{ old('price_per_unit') }}" min="0" step="1"
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"
                    placeholder="Product selling price per unit">
                @error('price_per_unit')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="reference" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Reference (e.g. Initial / Restock)</label>
                <input type="text" id="reference" name="reference" value="{{ old('reference', 'Initial / Restock') }}"
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
            <button type="submit" class="dash-btn dash-btn-brand" style="align-self:flex-start;">Save inventory</button>
        </div>
    </form>
</div>
@endif
@endsection
