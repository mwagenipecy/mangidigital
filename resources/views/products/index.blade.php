@extends('layouts.dashboard')

@section('title', __('Products'))

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Product</h1>
        <p class="dash-page-subtitle">Products you sell (register after adding product categories)</p>
    </div>
    <a href="{{ route('products.create') }}" class="dash-btn dash-btn-brand" wire:navigate>
        <flux:icon.plus class="size-4" />
        Add product
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
            <div class="dash-card-title">Products</div>
            <div class="dash-card-subtitle">Items you sell — not inventory/stock</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <div class="dash-td-main">{{ $product->name }}</div>
                        @if($product->description)
                            <div class="dash-td-sub">{{ Str::limit($product->description, 40) }}</div>
                        @endif
                    </td>
                    <td><span class="dash-td-sub">{{ $product->productCategory->name ?? '—' }}</span></td>
                    <td><span class="dash-td-sub">{{ $product->unit ?? '—' }}</span></td>
                    <td style="text-align:right;">
                        <a href="{{ route('products.edit', $product) }}" class="dash-btn dash-btn-outline" style="padding:6px 12px;font-size:.8rem;" wire:navigate>Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:24px;color:var(--dash-muted);">No products yet. <a href="{{ route('product-categories.index') }}" class="text-[var(--dash-brand)]" wire:navigate>Add a product category</a> first, then <a href="{{ route('products.create') }}" class="text-[var(--dash-brand)]" wire:navigate>add products</a>.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($products->previousPageUrl())
                <a href="{{ $products->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>
            @if($products->nextPageUrl())
                <a href="{{ $products->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
