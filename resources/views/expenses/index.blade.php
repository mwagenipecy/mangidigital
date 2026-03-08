@extends('layouts.dashboard')

@section('title', 'Expenses')

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

<form method="GET" action="{{ route('expenses.index') }}" style="margin-bottom:16px;">
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        <label for="category" style="font-size:.85rem;color:var(--dash-muted);">Category</label>
        <select id="category" name="category" style="padding:8px 12px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            <option value="">All</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ request('category') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="dash-btn dash-btn-outline" style="font-size:.85rem;">Filter</button>
    </div>
</form>

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
