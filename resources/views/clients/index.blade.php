@extends('layouts.dashboard')

@section('title', 'Clients')

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Clients</h1>
        <p class="dash-page-subtitle">Name and phone — used for sales and receipts</p>
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

<div class="dash-card" style="margin-bottom:20px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Add client</div>
            <div class="dash-card-subtitle">Name and phone are required for sales</div>
        </div>
    </div>
    <form action="{{ route('clients.store') }}" method="POST" style="padding:0 20px 20px;">
        @csrf
        <div style="display:flex;flex-wrap:wrap;gap:16px;align-items:flex-end;">
            <div>
                <label for="name" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required style="width:200px;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                @error('name')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Phone *</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required style="width:160px;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                @error('phone')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" style="width:200px;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
            <button type="submit" class="dash-btn dash-btn-brand">Add client</button>
        </div>
    </form>
</div>

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Clients list</div>
            <div class="dash-card-subtitle">Select client when recording a sale</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $c)
                <tr>
                    <td><span class="dash-td-main">{{ $c->name }}</span></td>
                    <td><span class="dash-td-sub">{{ $c->phone }}</span></td>
                    <td><span class="dash-td-sub">{{ $c->email ?? '—' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center;padding:24px;color:var(--dash-muted);">No clients yet. Add name and phone above, or add when recording a sale.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($clients->previousPageUrl())
                <a href="{{ $clients->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $clients->currentPage() }} of {{ $clients->lastPage() }}</span>
            @if($clients->nextPageUrl())
                <a href="{{ $clients->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
