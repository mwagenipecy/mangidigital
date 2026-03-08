@extends('layouts.dashboard')

@section('title', __('Inventory'))

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Inventory</h1>
        <p class="dash-page-subtitle">Stock by product and store — view and record inventory</p>
    </div>
    <a href="{{ route('inventory.create') }}" class="dash-btn dash-btn-brand" wire:navigate>
        <flux:icon.plus class="size-4" />
        Record inventory
    </a>
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

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Inventory list</div>
            <div class="dash-card-subtitle">By product and store</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Store</th>
                    <th>Quantity</th>
                    <th>Price (TZS)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventories as $inv)
                <tr>
                    <td>
                        <div class="dash-td-main">{{ $inv->product->name ?? '—' }}</div>
                        @if($inv->product?->productCategory)
                            <div class="dash-td-sub">{{ $inv->product->productCategory->name }}</div>
                        @endif
                    </td>
                    <td><span class="dash-td-main">{{ $inv->store->name ?? '—' }}</span></td>
                    <td><span class="dash-td-amount">{{ number_format($inv->quantity, 0) }} {{ $inv->product->unit ?? '' }}</span></td>
                    <td><span class="dash-td-sub">{{ $inv->price_per_unit !== null ? number_format($inv->price_per_unit, 0) : ($inv->product->price ? number_format($inv->product->price, 0) : '—') }}</span></td>
                    <td>
                        @if($inv->is_out_of_stock)
                            <span class="dash-pill dash-pill-red">Out of stock</span>
                        @else
                            <span class="dash-pill dash-pill-green">In stock</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('inventory.show', $inv) }}" class="dash-btn dash-btn-outline" style="padding:5px 12px;font-size:.75rem;" wire:navigate>
                            View
                            <flux:icon.arrow-right class="size-3" style="margin-left:4px" />
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:24px;color:var(--dash-muted);">No inventory yet. <a href="{{ route('inventory.create') }}" class="text-[var(--dash-brand)]" wire:navigate>Record inventory</a> (select product, store, quantity, price).</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($inventories->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($inventories->previousPageUrl())
                <a href="{{ $inventories->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $inventories->currentPage() }} of {{ $inventories->lastPage() }}</span>
            @if($inventories->nextPageUrl())
                <a href="{{ $inventories->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
