@extends('layouts.dashboard')

@section('title', __('Administration'))

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Administration</h1>
        <p class="dash-page-subtitle">Manage registrations, subscriptions and income overview</p>
    </div>
</div>

@if(session('success'))
    <div class="dash-card" style="margin-bottom:16px;background:var(--dash-brand-10);border-color:var(--dash-brand);">
        <p style="margin:0;font-size:.9rem;color:var(--dash-ink);">{{ session('success') }}</p>
    </div>
@endif

{{-- Income summary cards --}}
<div class="dash-kpi-grid">
    <div class="dash-kpi-card" style="--kpi-color:var(--dash-brand);--kpi-bg:var(--dash-brand-10)">
        <div class="dash-kpi-top">
            <div class="dash-kpi-icon"><flux:icon.banknotes class="size-5" /></div>
        </div>
        <div class="dash-kpi-value">{{ number_format($incomeTotal, 0) }}</div>
        <div class="dash-kpi-label">Total income (TZS)</div>
    </div>
    <div class="dash-kpi-card" style="--kpi-color:var(--dash-ok);--kpi-bg:rgba(34,197,94,.08)">
        <div class="dash-kpi-top">
            <div class="dash-kpi-icon"><flux:icon.calendar-days class="size-5" /></div>
        </div>
        <div class="dash-kpi-value">{{ number_format($incomeThisMonth, 0) }}</div>
        <div class="dash-kpi-label">This month (TZS)</div>
    </div>
    <div class="dash-kpi-card" style="--kpi-color:var(--dash-warn);--kpi-bg:rgba(245,158,11,.08)">
        <div class="dash-kpi-top">
            <div class="dash-kpi-icon"><flux:icon.chart-bar class="size-5" /></div>
        </div>
        <div class="dash-kpi-value">{{ number_format($incomeThisYear, 0) }}</div>
        <div class="dash-kpi-label">This year (TZS)</div>
    </div>
    <div class="dash-kpi-card" style="--kpi-color:var(--dash-purple);--kpi-bg:rgba(139,92,246,.1)">
        <div class="dash-kpi-top">
            <div class="dash-kpi-icon"><flux:icon.users class="size-5" /></div>
        </div>
        <div class="dash-kpi-value">{{ $registrations->total() }}</div>
        <div class="dash-kpi-label">Registrations</div>
    </div>
</div>

{{-- Registrations table --}}
<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Registered users</div>
            <div class="dash-card-subtitle">Approve, suspend or terminate. View organization to see user count.</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Organization</th>
                    <th>Subscription start</th>
                    <th>Subscription end</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registrations as $reg)
                <tr>
                    <td>
                        <div class="dash-td-name">
                            <div class="dash-td-avatar">{{ $reg->initials() }}</div>
                            <div>
                                <div class="dash-td-main">{{ $reg->name }}</div>
                                <div class="dash-td-sub">{{ $reg->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($reg->organization)
                            <a href="{{ route('admin.organizations.show', $reg->organization) }}" class="dash-btn-outline dash-btn" style="padding:4px 10px;font-size:.8rem;" wire:navigate>
                                {{ $reg->organization->name }}
                                <flux:icon.arrow-right class="size-3" style="margin-left:4px" />
                            </a>
                        @else
                            <span class="dash-td-sub">—</span>
                        @endif
                    </td>
                    <td>{{ $reg->organization?->subscription_start?->format('d M Y') ?? '—' }}</td>
                    <td>{{ $reg->organization?->subscription_end?->format('d M Y') ?? '—' }}</td>
                    <td>
                        @if($reg->status === 'approved')
                            <span class="dash-pill dash-pill-green">Approved</span>
                        @elseif($reg->status === 'suspended')
                            <span class="dash-pill dash-pill-yellow">Suspended</span>
                        @elseif($reg->status === 'terminated')
                            <span class="dash-pill dash-pill-red">Terminated</span>
                        @else
                            <span class="dash-pill dash-pill-blue">Pending</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;">
                            <a href="{{ route('admin.users.show', $reg) }}" class="dash-btn dash-btn-outline" style="padding:5px 12px;font-size:.75rem;" wire:navigate>
                                View details
                                <flux:icon.arrow-right class="size-3" style="margin-left:4px" />
                            </a>
                            @if($reg->status === 'pending_approval')
                                <form action="{{ route('admin.users.approve', $reg) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="dash-btn dash-btn-brand" style="padding:5px 12px;font-size:.75rem;">Approve</button>
                                </form>
                            @endif
                            @if(!in_array($reg->status, ['suspended', 'terminated']))
                                <form action="{{ route('admin.users.suspend', $reg) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="dash-btn dash-btn-outline" style="padding:5px 12px;font-size:.75rem;">Suspend</button>
                                </form>
                            @endif
                            @if($reg->status !== 'terminated')
                                <form action="{{ route('admin.users.terminate', $reg) }}" method="POST" class="inline" onsubmit="return confirm('Terminate this user and their organization?');">
                                    @csrf
                                    <button type="submit" class="dash-btn" style="padding:5px 12px;font-size:.75rem;background:var(--dash-danger);color:#fff;border:none;">Terminate</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:24px;color:var(--dash-muted);">No registrations yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($registrations->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($registrations->previousPageUrl())
                <a href="{{ $registrations->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $registrations->currentPage() }} of {{ $registrations->lastPage() }}</span>
            @if($registrations->nextPageUrl())
                <a href="{{ $registrations->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
