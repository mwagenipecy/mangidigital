@extends('layouts.dashboard')

@section('title', 'Payments')

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Client Payments</h1>
        <p class="dash-page-subtitle">Track plan targets, installment progress, reminders, and completion</p>
    </div>
</div>

<div class="dash-card" style="margin-bottom:16px;">
    <form method="GET" action="{{ route('payments.index') }}">
        <div class="dash-form-grid dash-form-grid--4">
            <div class="dash-form-field">
                <label for="search">Search by Client Name</label>
                <input type="text" id="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="e.g. Amina">
            </div>
            <div class="dash-form-field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All</option>
                    <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress (&lt; 50%)</option>
                    <option value="over_50" {{ ($filters['status'] ?? '') === 'over_50' ? 'selected' : '' }}>50%+ Progress</option>
                    <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="dash-form-field">
                <label for="year">Year</label>
                <select id="year" name="year">
                    <option value="">All years</option>
                    @for($y = now()->year; $y >= now()->year - 8; $y--)
                        <option value="{{ $y }}" {{ (int) ($filters['year'] ?? 0) === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="dash-form-field" style="display:flex;align-items:flex-end;gap:8px;">
                <button type="submit" class="dash-btn dash-btn-brand">Apply Filters</button>
                <a href="{{ route('payments.index') }}" class="dash-btn dash-btn-outline">Reset</a>
            </div>
        </div>
        <div class="dash-form-grid dash-form-grid--4" style="margin-top:10px;">
            <div class="dash-form-field">
                <label for="date_from">Date From</label>
                <input type="date" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="dash-form-field">
                <label for="date_to">Date To</label>
                <input type="date" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
            </div>
        </div>
    </form>
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
        <div class="dash-kpi-top">
            <div class="dash-kpi-icon"><flux:icon.clock class="size-5 text-[var(--dash-brand)]" /></div>
        </div>
        <div class="dash-kpi-value">{{ $metrics['active'] }}</div>
        <div class="dash-kpi-label">In Progress (&lt; 50%)</div>
    </div>
    <div class="dash-kpi-card" style="--kpi-color: var(--dash-warn); --kpi-bg: rgba(245,158,11,.12);">
        <div class="dash-kpi-top">
            <div class="dash-kpi-icon"><flux:icon.bolt class="size-5 text-[var(--dash-warn)]" /></div>
        </div>
        <div class="dash-kpi-value">{{ $metrics['over_half'] }}</div>
        <div class="dash-kpi-label">Progress 50%+</div>
    </div>
    <div class="dash-kpi-card" style="--kpi-color: var(--dash-ok); --kpi-bg: rgba(34,197,94,.12);">
        <div class="dash-kpi-top">
            <div class="dash-kpi-icon"><flux:icon.check-circle class="size-5 text-[var(--dash-ok)]" /></div>
        </div>
        <div class="dash-kpi-value">{{ $metrics['completed'] }}</div>
        <div class="dash-kpi-label">Completed / Closed</div>
    </div>
</div>

<div class="dash-card" style="margin-bottom:16px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Create Client Plan</div>
            <div class="dash-card-subtitle">Example: Plan target TZS 2,300,000 then collect installments</div>
        </div>
    </div>
    <form action="{{ route('payments.plans.store') }}" method="POST" class="js-loading-form">
        @csrf
        <div class="dash-form-grid dash-form-grid--4">
            <div class="dash-form-field">
                <label for="client_id">Client *</label>
                <select id="client_id" name="client_id" required>
                    <option value="">Select client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }} ({{ $client->phone }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="dash-form-field">
                <label for="plan_name">Plan Name *</label>
                <input type="text" id="plan_name" name="plan_name" value="{{ old('plan_name', 'Installment Plan') }}" required>
            </div>
            <div class="dash-form-field">
                <label for="goal_amount">Target Amount (TZS) *</label>
                <input type="number" id="goal_amount" name="goal_amount" min="1" step="1" value="{{ old('goal_amount') }}" required>
            </div>
            <div class="dash-form-field">
                <label for="started_at">Start Date</label>
                <input type="date" id="started_at" name="started_at" value="{{ old('started_at', now()->toDateString()) }}">
            </div>
        </div>
        <div class="dash-form-field" style="margin-top:12px;">
            <label for="plan_notes">Notes</label>
            <textarea id="plan_notes" name="notes" rows="2">{{ old('notes') }}</textarea>
        </div>
        <div class="dash-form-actions">
            <button type="submit" class="dash-btn dash-btn-brand js-loading-btn" data-loading-text="Creating plan...">Create Plan</button>
        </div>
    </form>
</div>

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Payment Plans</div>
            <div class="dash-card-subtitle">Record installments, monitor progress, and send reminders</div>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:12px;">
        @forelse($plans as $plan)
            @php
                $paid = (float) ($plan->installments_sum_amount ?? 0);
                $goal = (float) $plan->goal_amount;
                $progress = $goal > 0 ? min(100, ($paid / $goal) * 100) : 0;
                $remaining = max(0, $goal - $paid);
                $isCompleted = $plan->status === 'closed' || $progress >= 100;
            @endphp
            <div style="border:1px solid var(--dash-border);border-radius:var(--dash-r-sm);padding:14px;">
                <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div>
                        <div class="dash-td-main">
                            <a href="{{ route('payments.show', $plan) }}" wire:navigate style="color:inherit;text-decoration:none;">
                                {{ $plan->plan_name }}
                            </a>
                        </div>
                        <div class="dash-td-sub">{{ $plan->client?->name }} · {{ $plan->client?->phone }}</div>
                    </div>
                    <div>
                        @if($isCompleted)
                            <span class="dash-pill dash-pill-green">Completed</span>
                        @elseif($progress >= 50)
                            <span class="dash-pill dash-pill-yellow">50%+ Progress</span>
                        @else
                            <span class="dash-pill dash-pill-blue">In Progress</span>
                        @endif
                    </div>
                </div>

                <div style="margin-top:10px;display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;">
                    <div><span class="dash-td-sub">Target</span><div class="dash-td-amount">TZS {{ number_format($goal, 0) }}</div></div>
                    <div><span class="dash-td-sub">Paid</span><div class="dash-td-amount">TZS {{ number_format($paid, 0) }}</div></div>
                    <div><span class="dash-td-sub">Remaining</span><div class="dash-td-amount">TZS {{ number_format($remaining, 0) }}</div></div>
                    <div><span class="dash-td-sub">Progress</span><div class="dash-td-amount">{{ number_format($progress, 1) }}%</div></div>
                </div>

                <div style="height:9px;border-radius:999px;background:#edf6f8;margin-top:10px;overflow:hidden;">
                    <div style="height:100%;width:{{ number_format($progress, 2, '.', '') }}%;background:{{ $isCompleted ? '#22c55e' : ($progress >= 50 ? '#f59e0b' : '#2AA5BD') }};"></div>
                </div>

                <div style="display:grid;grid-template-columns:1fr auto;gap:10px;align-items:end;margin-top:12px;">
                    <div class="dash-td-sub">Choose method in modal: Cash, Bank, or Mobile Wallet.</div>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <button
                            type="button"
                            class="dash-btn dash-btn-brand"
                            onclick="openInstallmentModal('{{ $plan->id }}', '{{ e($plan->plan_name) }}', '{{ e($plan->client?->name) }}')"
                            {{ $isCompleted ? 'disabled' : '' }}
                        >
                            Record Payment
                        </button>
                        <a href="{{ route('payments.show', $plan) }}" class="dash-btn dash-btn-outline" wire:navigate>View Transactions</a>
                        <form action="{{ route('payments.remind', $plan) }}" method="POST" class="js-loading-form" style="margin:0;">
                            @csrf
                            <button type="submit" class="dash-btn dash-btn-outline js-loading-btn" data-loading-text="Sending..." {{ $isCompleted ? 'disabled' : '' }}>
                                Remind Client
                            </button>
                        </form>
                    </div>
                </div>

                @if($plan->last_reminded_at)
                    <div class="dash-td-sub" style="margin-top:8px;">Last reminder: {{ $plan->last_reminded_at->format('d M Y H:i') }}</div>
                @endif
            </div>
        @empty
            <div style="text-align:center;padding:24px;color:var(--dash-muted);border:1px dashed var(--dash-border);border-radius:var(--dash-r-sm);">
                No payment plans yet. Create one above to start tracking installments.
            </div>
        @endforelse
    </div>

    @if($plans->hasPages())
        <div style="padding-top:12px;display:flex;justify-content:center;gap:8px;">
            @if($plans->previousPageUrl())
                <a href="{{ $plans->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $plans->currentPage() }} of {{ $plans->lastPage() }}</span>
            @if($plans->nextPageUrl())
                <a href="{{ $plans->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>

<div class="dash-modal-overlay" id="installmentModal" role="dialog" aria-modal="true" aria-labelledby="installmentModalTitle" onclick="if(event.target===this) this.classList.remove('show')">
    <div class="dash-modal-dialog" onclick="event.stopPropagation()">
        <div class="dash-modal-header">
            <h2 class="dash-modal-title" id="installmentModalTitle">Record Installment Payment</h2>
            <button type="button" class="dash-modal-close" onclick="closeInstallmentModal()" aria-label="Close">&times;</button>
        </div>
        <div class="dash-modal-body">
            <div class="dash-td-sub" id="installmentModalMeta" style="margin-bottom:10px;"></div>
            <form action="{{ route('payments.installments.store') }}" method="POST" class="js-loading-form">
                @csrf
                <input type="hidden" name="plan_id" id="installment_plan_id">
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Amount (TZS) *</label>
                        <input type="number" name="amount" min="1" step="1" required style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);">
                    </div>
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Payment Method *</label>
                        <select name="payment_method" required style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);">
                            <option value="cash">Cash</option>
                            <option value="bank">Bank</option>
                            <option value="mobile_wallet">Mobile Wallet</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Reference (optional)</label>
                        <input type="text" name="payment_reference" placeholder="Bank slip no / wallet transaction id" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);">
                    </div>
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Date *</label>
                        <input type="date" name="paid_at" value="{{ now()->toDateString() }}" required style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);">
                    </div>
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Note</label>
                        <input type="text" name="notes" placeholder="Optional note" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);">
                    </div>
                    <div style="display:flex;gap:10px;justify-content:flex-end;">
                        <button type="button" class="dash-btn dash-btn-outline" onclick="closeInstallmentModal()">Cancel</button>
                        <button type="submit" class="dash-btn dash-btn-brand js-loading-btn" data-loading-text="Saving...">Save Payment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openInstallmentModal(planId, planName, clientName) {
    document.getElementById('installment_plan_id').value = planId;
    document.getElementById('installmentModalMeta').textContent = 'Client: ' + clientName + ' · Plan: ' + planName;
    document.getElementById('installmentModal').classList.add('show');
}

function closeInstallmentModal() {
    document.getElementById('installmentModal').classList.remove('show');
}

document.addEventListener('submit', function (event) {
    const form = event.target.closest('.js-loading-form');
    if (!form) return;

    const buttons = form.querySelectorAll('.js-loading-btn');
    buttons.forEach((button) => {
        if (button.disabled) return;
        button.dataset.originalText = button.textContent.trim();
        button.disabled = true;
        button.textContent = button.dataset.loadingText || 'Please wait...';
    });
});
</script>
@endpush
