@extends('layouts.dashboard')

@section('title', __('Stores'))

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Register store/shop</h1>
        <p class="dash-page-subtitle">Your organization's stores or shops</p>
    </div>
    <a href="{{ route('stores.create') }}" class="dash-btn dash-btn-brand" wire:navigate>
        <flux:icon.plus class="size-4" />
        Add store
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
            <div class="dash-card-title">Stores</div>
            <div class="dash-card-subtitle">Register and manage your stores or shops</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stores as $store)
                <tr>
                    <td><span class="dash-td-main">{{ $store->name }}</span></td>
                    <td><span class="dash-td-sub">{{ $store->address ?? '—' }}</span></td>
                    <td><span class="dash-td-sub">{{ $store->phone ?? '—' }}</span></td>
                    <td><span class="dash-td-sub">{{ $store->email ?? '—' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:24px;color:var(--dash-muted);">No stores yet. <a href="{{ route('stores.create') }}" class="text-[var(--dash-brand)]" wire:navigate>Add your first store</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stores->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($stores->previousPageUrl())
                <a href="{{ $stores->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $stores->currentPage() }} of {{ $stores->lastPage() }}</span>
            @if($stores->nextPageUrl())
                <a href="{{ $stores->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
