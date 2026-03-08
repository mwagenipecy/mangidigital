@extends('layouts.dashboard')

@section('title', __('Product categories'))

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Product category</h1>
        <p class="dash-page-subtitle">Register categories before adding products</p>
    </div>
    <a href="{{ route('product-categories.create') }}" class="dash-btn dash-btn-brand" wire:navigate>
        <flux:icon.plus class="size-4" />
        Add category
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
            <div class="dash-card-title">Categories</div>
            <div class="dash-card-subtitle">Product categories for your organization</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Products</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td><span class="dash-td-main">{{ $cat->name }}</span></td>
                    <td><span class="dash-td-sub">{{ Str::limit($cat->description, 50) ?? '—' }}</span></td>
                    <td><span class="dash-td-amount">{{ $cat->products_count ?? 0 }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center;padding:24px;color:var(--dash-muted);">No categories yet. <a href="{{ route('product-categories.create') }}" class="text-[var(--dash-brand)]" wire:navigate>Add your first category</a> before adding products.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($categories->previousPageUrl())
                <a href="{{ $categories->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $categories->currentPage() }} of {{ $categories->lastPage() }}</span>
            @if($categories->nextPageUrl())
                <a href="{{ $categories->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
