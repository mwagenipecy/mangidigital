@extends('layouts.dashboard')

@section('title', 'Plan Transactions')

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Plan Transactions</h1>
        <p class="dash-page-subtitle">{{ $plan->client?->name }} · {{ $plan->plan_name }}</p>
    </div>
    <a href="{{ route('payments.index') }}" class="dash-btn dash-btn-outline" wire:navigate>← Back to Payments</a>
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

<div class="dash-kpi-grid dash-kpi-grid--three">
    <div class="dash-kpi-card" style="--kpi-color: var(--dash-brand); --kpi-bg: var(--dash-brand-10);">
        <div class="dash-kpi-value">TZS {{ number_format($goal, 0) }}</div>
        <div class="dash-kpi-label">Target Amount</div>
    </div>
    <div class="dash-kpi-card" style="--kpi-color: var(--dash-ok); --kpi-bg: rgba(34,197,94,.12);">
        <div class="dash-kpi-value">TZS {{ number_format($paid, 0) }}</div>
        <div class="dash-kpi-label">Total Paid</div>
    </div>
    <div class="dash-kpi-card" style="--kpi-color: var(--dash-warn); --kpi-bg: rgba(245,158,11,.12);">
        <div class="dash-kpi-value">{{ number_format($progress, 1) }}%</div>
        <div class="dash-kpi-label">Progress</div>
    </div>
</div>

<div class="dash-card" style="margin-bottom:16px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Progress Summary</div>
            <div class="dash-card-subtitle">Remaining: TZS {{ number_format($remaining, 0) }}</div>
        </div>
        <span class="dash-pill {{ $progress >= 100 || $plan->status === 'closed' ? 'dash-pill-green' : ($progress >= 50 ? 'dash-pill-yellow' : 'dash-pill-blue') }}">
            {{ $progress >= 100 || $plan->status === 'closed' ? 'Completed' : ($progress >= 50 ? '50%+ Progress' : 'In Progress') }}
        </span>
    </div>
    <div style="height:10px;border-radius:999px;background:#edf6f8;overflow:hidden;">
        <div style="height:100%;width:{{ number_format($progress, 2, '.', '') }}%;background:{{ $progress >= 100 || $plan->status === 'closed' ? '#22c55e' : ($progress >= 50 ? '#f59e0b' : '#2AA5BD') }};"></div>
    </div>
</div>

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">All Recorded Transactions</div>
            <div class="dash-card-subtitle">Only for {{ $plan->client?->name }}</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Notes</th>
                    <th>Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $txn)
                    <tr>
                        <td><span class="dash-td-sub">{{ optional($txn->paid_at)->format('d M Y') }}</span></td>
                        <td><span class="dash-td-amount">TZS {{ number_format((float) $txn->amount, 0) }}</span></td>
                        <td>
                            <span class="dash-td-sub">
                                {{ str($txn->payment_method ?? 'cash')->replace('_', ' ')->title() }}
                                @if($txn->payment_reference)
                                    · {{ $txn->payment_reference }}
                                @endif
                            </span>
                        </td>
                        <td><span class="dash-td-sub">{{ $txn->notes ?: '—' }}</span></td>
                        <td><span class="dash-td-sub">{{ $txn->recordedBy?->name ?: 'System' }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:24px;color:var(--dash-muted);">
                            No transactions recorded yet for this installment plan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transactions->hasPages())
        <div style="padding-top:12px;display:flex;justify-content:center;gap:8px;">
            @if($transactions->previousPageUrl())
                <a href="{{ $transactions->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}</span>
            @if($transactions->nextPageUrl())
                <a href="{{ $transactions->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
