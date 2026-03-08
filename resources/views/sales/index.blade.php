@extends('layouts.dashboard')

@section('title', __('Sales'))

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
