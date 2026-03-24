@extends('layouts.dashboard')

@section('title', 'Expenses')

@php
    $fmt = fn ($v) => number_format((float) $v, 0);
@endphp

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Expenses</h1>
        <p class="dash-page-subtitle">List all expenses by category — add reason and upload receipts</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('expense-categories.index') }}" class="dash-btn dash-btn-outline" wire:navigate>Categories</a>
        <a href="{{ route('expenses.create') }}" class="dash-btn dash-btn-brand" wire:navigate>
            <flux:icon.plus class="size-4" />
            Add expense
        </a>
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

<form method="GET" action="{{ route('expenses.index') }}" class="dash-card" style="margin-bottom:20px;padding:16px 20px;">
    <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        <div>
            <label for="category" style="display:block;font-size:.75rem;font-weight:700;color:var(--dash-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;">Category</label>
            <select id="category" name="category" style="padding:9px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            <option value="">All</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ request('category') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
            </select>
        </div>
        <div>
            <label for="month" style="display:block;font-size:.75rem;font-weight:700;color:var(--dash-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;">Month</label>
        <input
                type="month"
                id="month"
                name="month"
                value="{{ request('month', $range['month']) }}"
                style="padding:9px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"
            >
        </div>
        <div>
            <label for="from" style="display:block;font-size:.75rem;font-weight:700;color:var(--dash-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;">From</label>
            <input type="date" id="from" name="from" value="{{ request('from', $range['from']?->format('Y-m-d')) }}" style="padding:9px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
        </div>
        <div>
            <label for="to" style="display:block;font-size:.75rem;font-weight:700;color:var(--dash-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;">To</label>
            <input type="date" id="to" name="to" value="{{ request('to', $range['to']?->format('Y-m-d')) }}" style="padding:9px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
        </div>
        <button type="submit" class="dash-btn dash-btn-brand">Apply</button>
        <a href="{{ route('expenses.index') }}" class="dash-btn dash-btn-outline" wire:navigate>Reset</a>
        <div style="margin-left:auto;font-size:.82rem;color:var(--dash-muted);font-weight:600;">
            {{ $range['from_display'] }}{{ $range['to_display'] ? ' — '.$range['to_display'] : '' }}
        </div>
    </div>
</form>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:16px;">
    <div class="dash-card" style="margin-bottom:0;padding:18px;border-left:4px solid #dc2626;">
        <div style="font-size:.72rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">Total expenses</div>
        <div style="font-size:1.5rem;font-weight:900;color:#dc2626;margin-top:6px;">{{ $fmt($stats['total_expense']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
        <div style="font-size:.8rem;color:var(--dash-muted);margin-top:4px;">{{ $range['from_display'] }} — {{ $range['to_display'] }}</div>
    </div>
    <div class="dash-card" style="margin-bottom:0;padding:18px;">
        <div style="font-size:.72rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">Expenses count</div>
        <div style="font-size:1.5rem;font-weight:900;color:var(--dash-ink);margin-top:6px;">{{ $stats['expense_count'] }}</div>
    </div>
    <div class="dash-card" style="margin-bottom:0;padding:18px;">
        <div style="font-size:.72rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">Average expense</div>
        <div style="font-size:1.5rem;font-weight:900;color:#b45309;margin-top:6px;">{{ $fmt($stats['avg_expense']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
    </div>
    <div class="dash-card" style="margin-bottom:0;padding:18px;">
        <div style="font-size:.72rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">Highest expense day</div>
        @if($stats['highest_day'])
            <div style="font-size:1.1rem;font-weight:900;color:var(--dash-ink);margin-top:6px;">{{ \Carbon\Carbon::parse($stats['highest_day'])->format('d M Y') }}</div>
            <div style="font-size:.9rem;color:#dc2626;font-weight:700;">{{ $fmt($stats['highest_day_total']) }} TZS</div>
        @else
            <div style="font-size:.95rem;color:var(--dash-muted);margin-top:10px;">No data</div>
        @endif
    </div>
</div>

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">All expenses</div>
            <div class="dash-card-subtitle">Category, reason, amount, date — receipt download if uploaded</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Reason</th>
                    <th>Amount (TZS)</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $exp)
                <tr>
                    <td><span class="dash-td-sub">{{ $exp->expense_date?->format('d M Y') }}</span></td>
                    <td><span class="dash-td-main">{{ $exp->expenseCategory?->name ?? '—' }}</span></td>
                    <td><span class="dash-td-sub" style="max-width:280px;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $exp->reason }}</span></td>
                    <td><span class="dash-td-amount">{{ number_format($exp->amount, 0) }}</span></td>
                    <td>
                        @if($exp->hasReceipt())
                            <a href="{{ route('expenses.receipt', $exp) }}" class="dash-btn dash-btn-outline" style="padding:5px 10px;font-size:.75rem;" target="_blank" rel="noopener">Download</a>
                        @else
                            <span class="dash-td-sub">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:24px;color:var(--dash-muted);">
                        No expenses yet.
                        @if($categories->isEmpty())
                            <a href="{{ route('expense-categories.index') }}" class="text-[var(--dash-brand)]" wire:navigate>Add expense categories</a> first, then
                        @endif
                        <a href="{{ route('expenses.create') }}" class="text-[var(--dash-brand)]" wire:navigate>Add expense</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($expenses->previousPageUrl())
                <a href="{{ $expenses->appends(request()->query())->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $expenses->currentPage() }} of {{ $expenses->lastPage() }}</span>
            @if($expenses->nextPageUrl())
                <a href="{{ $expenses->appends(request()->query())->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
