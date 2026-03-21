@extends('layouts.dashboard')

@section('title', __('Clearance & transport'))

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Clearance & transport</h1>
        <p class="dash-page-subtitle">Clearance and forward companies, international and local transport — same page with identifier</p>
    </div>
    <a href="{{ route('service-providers.create') }}" class="dash-btn dash-btn-brand" wire:navigate>
        <flux:icon.plus class="size-4" />
        Add company
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
            <div class="dash-card-title">Service providers</div>
            <div class="dash-card-subtitle">International transport, local transport, clearance & forwarding</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type / identifier</th>
                    <th>Category</th>
                    <th>Contact</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($providers as $p)
                <tr>
                    <td><span class="dash-td-main">{{ $p->name }}</span></td>
                    <td>
                        @if($p->type === \App\Models\ServiceProvider::TYPE_INTERNATIONAL_TRANSPORT)
                            <span class="dash-pill" style="background:var(--dash-brand-10);color:var(--dash-brand);">International transport</span>
                        @elseif($p->type === \App\Models\ServiceProvider::TYPE_LOCAL_TRANSPORT)
                            <span class="dash-pill" style="background:#e0f2fe;color:#0369a1;">Local transport</span>
                        @else
                            <span class="dash-pill" style="background:#fef3c7;color:#b45309;">Clearance & forwarding</span>
                        @endif
                    </td>
                    <td><span class="dash-td-sub">{{ $p->productCategory?->name ?? 'All' }}</span></td>
                    <td><span class="dash-td-sub">{{ $p->contact_phone ?? $p->contact_email ?? '—' }}</span></td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('service-providers.edit', $p) }}" class="dash-btn dash-btn-outline" style="padding:5px 10px;font-size:.75rem;margin-right:4px;" wire:navigate>Edit</a>
                        <form action="{{ route('service-providers.destroy', $p) }}" method="POST" style="display:inline;" onsubmit="return confirm('Remove this provider?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dash-btn" style="padding:5px 10px;font-size:.75rem;background:var(--dash-danger);color:white;border:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:24px;color:var(--dash-muted);">No providers yet. <a href="{{ route('service-providers.create') }}" class="text-[var(--dash-brand)]" wire:navigate>Add clearance or transport company</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($providers->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($providers->previousPageUrl())
                <a href="{{ $providers->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $providers->currentPage() }} of {{ $providers->lastPage() }}</span>
            @if($providers->nextPageUrl())
                <a href="{{ $providers->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
