@extends('layouts.dashboard')

@section('title', $organization->name)

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('admin.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            Back to Administration
        </a>
        <h1 class="dash-page-title">{{ $organization->name }}</h1>
        <p class="dash-page-subtitle">
            Subscription {{ $organization->subscription_start?->format('d M Y') ?? '—' }} – {{ $organization->subscription_end?->format('d M Y') ?? '—' }}
            · Status: <strong>{{ ucfirst($organization->status) }}</strong>
        </p>
    </div>
</div>

<div class="dash-kpi-grid" style="grid-template-columns:repeat(1,1fr);max-width:320px;">
    <div class="dash-kpi-card" style="--kpi-color:var(--dash-brand);--kpi-bg:var(--dash-brand-10)">
        <div class="dash-kpi-top">
            <div class="dash-kpi-icon"><flux:icon.users class="size-5" /></div>
        </div>
        <div class="dash-kpi-value">{{ $organization->users_count ?? $organization->users()->count() }}</div>
        <div class="dash-kpi-label">Users in this organization</div>
    </div>
</div>

<div class="dash-card" style="margin-top:24px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Users</div>
            <div class="dash-card-subtitle">Members belonging to this organization or institution</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td>
                        <div class="dash-td-name">
                            <div class="dash-td-avatar">{{ $u->initials() }}</div>
                            <div class="dash-td-main">{{ $u->name }}</div>
                        </div>
                    </td>
                    <td><span class="dash-td-sub">{{ $u->email }}</span></td>
                    <td>
                        @if($u->status === 'approved')
                            <span class="dash-pill dash-pill-green">Approved</span>
                        @elseif($u->status === 'suspended')
                            <span class="dash-pill dash-pill-yellow">Suspended</span>
                        @elseif($u->status === 'terminated')
                            <span class="dash-pill dash-pill-red">Terminated</span>
                        @else
                            <span class="dash-pill dash-pill-blue">Pending</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center;padding:24px;color:var(--dash-muted);">No users in this organization.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
