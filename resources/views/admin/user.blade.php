@extends('layouts.dashboard')

@section('title', $registration->name . ' — Details')

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('admin.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            Back to Administration
        </a>
        <h1 class="dash-page-title">{{ $registration->name }}</h1>
        <p class="dash-page-subtitle">{{ $registration->email }} · Status: <strong>{{ ucfirst(str_replace('_', ' ', $registration->status)) }}</strong></p>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
        @if($registration->status === 'pending_approval')
            <form action="{{ route('admin.users.approve', $registration) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="dash-btn dash-btn-brand">Approve</button>
            </form>
        @endif
        @if(!in_array($registration->status, ['suspended', 'terminated']))
            <form action="{{ route('admin.users.suspend', $registration) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="dash-btn dash-btn-outline">Suspend</button>
            </form>
        @endif
        @if($registration->status !== 'terminated')
            <form action="{{ route('admin.users.terminate', $registration) }}" method="POST" class="inline" onsubmit="return confirm('Terminate this user and their organization?');">
                @csrf
                <button type="submit" class="dash-btn" style="background:var(--dash-danger);color:#fff;border:none;">Terminate</button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="dash-card" style="margin-bottom:16px;background:var(--dash-brand-10);border-color:var(--dash-brand);">
        <p style="margin:0;font-size:.9rem;color:var(--dash-ink);">{{ session('success') }}</p>
    </div>
@endif

{{-- Organization & subscription --}}
@if($registration->organization)
<div class="dash-card" style="margin-bottom:24px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Organization & subscription</div>
            <div class="dash-card-subtitle">Business and subscription period</div>
        </div>
        <a href="{{ route('admin.organizations.show', $registration->organization) }}" class="dash-btn dash-btn-outline" style="padding:6px 12px;font-size:.8rem;" wire:navigate>
            View organization ({{ $registration->organization->users()->count() }} users)
            <flux:icon.arrow-right class="size-3" style="margin-left:4px" />
        </a>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;">
        <div>
            <div class="dash-kpi-label">Organization</div>
            <div class="dash-td-main" style="font-size:1rem;">{{ $registration->organization->name }}</div>
        </div>
        <div>
            <div class="dash-kpi-label">Subscription start</div>
            <div class="dash-td-main" style="font-size:1rem;">{{ $registration->organization->subscription_start?->format('d M Y') ?? '—' }}</div>
        </div>
        <div>
            <div class="dash-kpi-label">Subscription end</div>
            <div class="dash-td-main" style="font-size:1rem;">{{ $registration->organization->subscription_end?->format('d M Y') ?? '—' }}</div>
        </div>
        <div>
            <div class="dash-kpi-label">Organization status</div>
            <span class="dash-pill {{ $registration->organization->status === 'active' ? 'dash-pill-green' : ($registration->organization->status === 'suspended' ? 'dash-pill-yellow' : 'dash-pill-red') }}">{{ ucfirst($registration->organization->status) }}</span>
        </div>
    </div>
</div>
@else
<div class="dash-card" style="margin-bottom:24px;">
    <p style="margin:0;color:var(--dash-muted);">No organization linked (registered before organization flow).</p>
</div>
@endif

{{-- Payment entries --}}
<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Payment entries</div>
            <div class="dash-card-subtitle">Payments made by or for this registration</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->recorded_at->format('d M Y H:i') }}</td>
                    <td><span class="dash-td-amount">{{ number_format($payment->amount, 0) }} {{ $payment->currency }}</span></td>
                    <td>{{ ucfirst($payment->payment_method ?? '—') }}</td>
                    <td>{{ $payment->reference ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:24px;color:var(--dash-muted);">No payment entries yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
