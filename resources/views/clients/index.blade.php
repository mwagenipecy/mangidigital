@extends('layouts.dashboard')

@section('title', 'Clients')

@php
    $fmt = fn ($v) => number_format((float) $v, 0);
@endphp

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Clients</h1>
        <p class="dash-page-subtitle">Name and phone — used for sales and receipts</p>
    </div>
    <button type="button" class="dash-btn dash-btn-brand" onclick="document.getElementById('addClientModal').classList.add('show')">Add client</button>
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

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:16px;">
    <div class="dash-card" style="margin-bottom:0;padding:18px;">
        <div style="font-size:.72rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">Total clients</div>
        <div style="font-size:1.5rem;font-weight:900;color:var(--dash-ink);margin-top:6px;">{{ $fmt($stats['total_clients']) }}</div>
    </div>
    <div class="dash-card" style="margin-bottom:0;padding:18px;">
        <div style="font-size:.72rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">With email</div>
        <div style="font-size:1.5rem;font-weight:900;color:#0369a1;margin-top:6px;">{{ $fmt($stats['clients_with_email']) }}</div>
    </div>
    <div class="dash-card" style="margin-bottom:0;padding:18px;">
        <div style="font-size:.72rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">New this month</div>
        <div style="font-size:1.5rem;font-weight:900;color:#15803d;margin-top:6px;">{{ $fmt($stats['new_this_month']) }}</div>
    </div>
</div>

<div class="dash-modal-overlay" id="addClientModal" role="dialog" aria-modal="true" aria-labelledby="addClientModalTitle" onclick="if(event.target===this) this.classList.remove('show')">
    <div class="dash-modal-dialog" onclick="event.stopPropagation()">
        <div class="dash-modal-header">
            <h2 class="dash-modal-title" id="addClientModalTitle">Add client</h2>
            <button type="button" class="dash-modal-close" onclick="document.getElementById('addClientModal').classList.remove('show')" aria-label="Close">&times;</button>
        </div>
        <div class="dash-modal-body">
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label for="name" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required style="width:100%;max-width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;box-sizing:border-box;">
                        @error('name')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Phone *</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required style="width:100%;max-width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;box-sizing:border-box;">
                        @error('phone')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" style="width:100%;max-width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;box-sizing:border-box;">
                    </div>
                    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                        <button type="button" class="dash-btn dash-btn-outline" onclick="document.getElementById('addClientModal').classList.remove('show')">Cancel</button>
                        <button type="submit" class="dash-btn dash-btn-brand">Add client</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
                    <td colspan="3" style="text-align:center;padding:24px;color:var(--dash-muted);">No clients yet. Click “Add client” to create one, or add when recording a sale.</td>
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

@if($errors->isNotEmpty())
<script>document.addEventListener('DOMContentLoaded', function() { document.getElementById('addClientModal').classList.add('show'); });</script>
@endif
@endsection
