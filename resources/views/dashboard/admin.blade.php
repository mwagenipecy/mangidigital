@extends('layouts.dashboard')

@section('title', __('Dashboard'))

@section('content')
@php
    $user = auth()->user();
    $firstName = $user ? collect(explode(' ', $user->name))->first() : 'Admin';
    $greeting = now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening');
@endphp
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">{{ $greeting }}, <em>{{ $firstName }}</em></h1>
        <p class="dash-page-subtitle">Platform overview — {{ now()->format('l, j F Y') }}</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <a href="{{ route('admin.index') }}" class="dash-btn dash-btn-brand">
            <flux:icon.cog-6-tooth class="size-4" />
            Admin panel
        </a>
    </div>
</div>

@if(session('success'))
    <div class="dash-card" style="margin-bottom:16px;background:var(--dash-brand-10);border-color:var(--dash-brand);">
        <p style="margin:0;font-size:.9rem;color:var(--dash-ink);">{{ session('success') }}</p>
    </div>
@endif

{{-- Row 1: Income + System users + Logged in today (5 cards) --}}
<div class="dash-admin-stats-grid">
    <x-dashboard.kpi-card :value="number_format($incomeTotal, 0)" label="Total income (TZS)" color="var(--dash-brand)" bg="var(--dash-brand-10)">
        <x-slot:icon><flux:icon.banknotes class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="number_format($incomeThisMonth, 0)" label="This month (TZS)" color="var(--dash-ok)" bg="rgba(34,197,94,.08)">
        <x-slot:icon><flux:icon.calendar-days class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="number_format($incomeThisYear, 0)" label="This year (TZS)" color="var(--dash-warn)" bg="rgba(245,158,11,.08)">
        <x-slot:icon><flux:icon.chart-bar class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="(string)$systemUsersTotal" label="System users" color="var(--dash-purple)" bg="rgba(139,92,246,.1)">
        <x-slot:icon><flux:icon.users class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="(string)$loggedInToday" label="Logged in today" color="var(--dash-ok)" bg="rgba(34,197,94,.08)">
        <x-slot:icon><flux:icon.signal class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>

    {{-- Row 2: Pending, Orgs, New this month + 2 quick actions (5 items, same grid) --}}
    <x-dashboard.kpi-card :value="(string)$usersPending" label="Pending approval" color="var(--dash-warn)" bg="rgba(245,158,11,.08)">
        <x-slot:icon><flux:icon.clock class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="(string)$organizationsTotal" label="Organizations" color="var(--dash-ok)" bg="rgba(34,197,94,.08)">
        <x-slot:icon><flux:icon.building-office-2 class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="(string)$usersThisMonth" label="New this month" color="var(--dash-brand)" bg="var(--dash-brand-10)">
        <x-slot:icon><flux:icon.user-plus class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    {{-- Quick actions: same icon style as KPI cards (38px box, 1.25rem icon) --}}
    <a href="{{ route('admin.index') }}" class="dash-kpi-card block text-center no-underline" style="--kpi-color:var(--dash-brand);--kpi-bg:var(--dash-brand-10);">
        <div class="dash-kpi-top" style="justify-content:flex-start;">
            <div class="dash-kpi-icon" style="color:var(--dash-brand);"><flux:icon.users class="size-5" /></div>
        </div>
        <div class="dash-kpi-value" style="font-size:1rem;margin-bottom:2px;">Manage users</div>
        <div class="dash-kpi-label">Approve, suspend, terminate</div>
    </a>
    <a href="{{ route('admin.index') }}" class="dash-kpi-card block text-center no-underline" style="--kpi-color:var(--dash-brand);--kpi-bg:var(--dash-brand-10);">
        <div class="dash-kpi-top" style="justify-content:flex-start;">
            <div class="dash-kpi-icon" style="color:var(--dash-brand);"><flux:icon.clipboard-document-list class="size-5" /></div>
        </div>
        <div class="dash-kpi-value" style="font-size:1rem;margin-bottom:2px;">View registrations</div>
        <div class="dash-kpi-label">All registered users</div>
    </a>
</div>

{{-- User status breakdown --}}
<div class="dash-admin-stats-grid dash-admin-stats-grid--small">
    <x-dashboard.kpi-card :value="(string)$usersApproved" label="Approved" color="var(--dash-ok)" bg="rgba(34,197,94,.08)">
        <x-slot:icon><flux:icon.check-circle class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="(string)$usersSuspended" label="Suspended" color="var(--dash-warn)" bg="rgba(245,158,11,.08)">
        <x-slot:icon><flux:icon.no-symbol class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
</div>

{{-- Admin quick links: compact row (dashboard.css only, no Tailwind) --}}
<div class="dash-card dash-admin-panel-row">
    <a href="{{ route('admin.index') }}" class="dash-admin-panel-link">
        <span class="dash-admin-panel-link__icon"><flux:icon.squares-2x2 class="size-5" /></span>
        <span class="dash-admin-panel-link__text">Admin panel — users, registrations, organizations ({{ $organizationsTotal }})</span>
    </a>
</div>

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Recent registrations</div>
            <div class="dash-card-subtitle">Latest users. Full list and actions in Admin panel.</div>
        </div>
        <a href="{{ route('admin.index') }}" class="dash-card-action">Admin panel →</a>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Organization</th>
                    <th>Status</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentUsers as $reg)
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
                    <td><span class="dash-td-sub">{{ $reg->created_at->format('d M Y') }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:24px;color:var(--dash-muted);">No registrations yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
