@extends('layouts.dashboard')

@section('title', __('Sales'))

@php
    $fmt = fn ($v) => number_format((float) $v, 0);
@endphp

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Sales</h1>
        <p class="dash-page-subtitle">Record sales by product and store — bulk prices, delivery, receipts</p>
    </div>
    <a href="{{ route('sales.create') }}" class="dash-btn dash-btn-brand" wire:navigate>
        <flux:icon.plus class="size-4" />
        New sale
    </a>
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

<form method="GET" action="{{ route('sales.index') }}" class="dash-card" style="margin-bottom:20px;padding:16px 20px;">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
        <div>
            <label for="range" style="display:block;font-size:.75rem;font-weight:700;color:var(--dash-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;">Period</label>
            <select name="range" id="range" onchange="if(this.value==='custom'){document.getElementById('salesCustomDates').style.display='flex';}else{document.getElementById('salesCustomDates').style.display='none';this.form.submit();}" style="padding:9px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                @foreach([
                    'today' => 'Today',
                    'this_week' => 'This week',
                    'this_month' => 'This month',
                    'last_month' => 'Last month',
                    'custom' => 'Custom range',
                ] as $key => $label)
                    <option value="{{ $key }}" {{ $range['preset'] === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div id="salesCustomDates" style="{{ $range['preset'] === 'custom' ? 'display:flex;' : 'display:none;' }}gap:10px;align-items:flex-end;">
            <div>
                <label for="from" style="display:block;font-size:.75rem;font-weight:700;color:var(--dash-muted);margin-bottom:4px;letter-spacing:.04em;text-transform:uppercase;">From</label>
                <input type="date" name="from" id="from" value="{{ $range['from']->format('Y-m-d') }}" style="padding:9px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
            <div>
                <label for="to" style="display:block;font-size:.75rem;font-weight:700;color:var(--dash-muted);margin-bottom:4px;letter-spacing:.04em;text-transform:uppercase;">To</label>
                <input type="date" name="to" id="to" value="{{ $range['to']->format('Y-m-d') }}" style="padding:9px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
            <button type="submit" class="dash-btn dash-btn-brand">Apply</button>
        </div>
        <div style="margin-left:auto;font-size:.82rem;color:var(--dash-muted);font-weight:600;">
            {{ $range['from_display'] }} — {{ $range['to_display'] }}
        </div>
    </div>
</form>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:16px;">
    <div class="dash-card" style="margin-bottom:0;padding:18px;">
        <div style="font-size:.72rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">Total sales</div>
        <div style="font-size:1.5rem;font-weight:900;color:#0369a1;margin-top:6px;">{{ $fmt($stats['total_sales']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
    </div>
    <div class="dash-card" style="margin-bottom:0;padding:18px;">
        <div style="font-size:.72rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">Sales count</div>
        <div style="font-size:1.5rem;font-weight:900;color:var(--dash-ink);margin-top:6px;">{{ $stats['sales_count'] }}</div>
    </div>
    <div class="dash-card" style="margin-bottom:0;padding:18px;">
        <div style="font-size:.72rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">Cost of goods sold</div>
        <div style="font-size:1.5rem;font-weight:900;color:#b45309;margin-top:6px;">{{ $fmt($stats['cogs']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
    </div>
    <div class="dash-card" style="margin-bottom:0;padding:18px;border-left:4px solid {{ $stats['net_profit'] >= 0 ? '#15803d' : '#dc2626' }};">
        <div style="font-size:.72rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">Net profit</div>
        <div style="font-size:1.5rem;font-weight:900;color:{{ $stats['net_profit'] >= 0 ? '#15803d' : '#dc2626' }};margin-top:6px;">{{ $fmt($stats['net_profit']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
    </div>
</div>

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Sales list</div>
            <div class="dash-card-subtitle">Receipt number, client, date, total</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Receipt</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Total (TZS)</th>
                    <th>Delivery</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                <tr>
                    <td><span class="dash-td-main">{{ $sale->receipt_number ?? '#' . $sale->id }}</span></td>
                    <td>
                        <div class="dash-td-main">{{ $sale->display_client_name }}</div>
                        <div class="dash-td-sub">{{ $sale->display_client_phone }}</div>
                    </td>
                    <td><span class="dash-td-sub">{{ $sale->sale_date?->format('d M Y') ?? '—' }}</span></td>
                    <td><span class="dash-td-amount">{{ number_format($sale->total, 0) }}</span></td>
                    <td>
                        @if($sale->delivery_requested)
                            <span class="dash-pill" style="font-size:.75rem;">Yes — {{ number_format($sale->delivery_cost, 0) }} TZS</span>
                        @else
                            <span class="dash-td-sub">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('sales.show', $sale) }}" class="dash-btn dash-btn-outline" style="padding:5px 12px;font-size:.75rem;" wire:navigate>View / Receipt</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:24px;color:var(--dash-muted);">No sales yet. <a href="{{ route('sales.create') }}" class="text-[var(--dash-brand)]" wire:navigate>Record a sale</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($sales->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($sales->previousPageUrl())
                <a href="{{ $sales->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $sales->currentPage() }} of {{ $sales->lastPage() }}</span>
            @if($sales->nextPageUrl())
                <a href="{{ $sales->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
