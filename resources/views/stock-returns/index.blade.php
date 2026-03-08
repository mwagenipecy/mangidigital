@extends('layouts.dashboard')

@section('title', 'Return stocks')

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Return stocks</h1>
        <p class="dash-page-subtitle">Record returns — affects sales and inventory (stock added back)</p>
    </div>
    <a href="{{ route('stock-returns.create') }}" class="dash-btn dash-btn-brand" wire:navigate>
        <flux:icon.plus class="size-4" />
        Record return
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
            <div class="dash-card-title">Returns</div>
            <div class="dash-card-subtitle">Linked to sale (optional), items returned to store — stock restored in inventory</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Return date</th>
                    <th>Sale (optional)</th>
                    <th>Items</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returns as $ret)
                <tr>
                    <td><span class="dash-td-main">{{ $ret->return_date?->format('d M Y') }}</span></td>
                    <td>
                        @if($ret->sale_id)
                            <a href="{{ route('sales.show', $ret->sale) }}" class="text-[var(--dash-brand)]" wire:navigate>{{ $ret->sale->receipt_number ?? '#' . $ret->sale_id }}</a>
                        @else
                            <span class="dash-td-sub">—</span>
                        @endif
                    </td>
                    <td>
                        @foreach($ret->items as $item)
                            <div class="dash-td-sub">{{ $item->display_product_name }} × {{ number_format($item->quantity, 0) }} → {{ $item->store?->name ?? '—' }}</div>
                        @endforeach
                    </td>
                    <td><span class="dash-td-sub">{{ Str::limit($ret->notes, 40) ?? '—' }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:24px;color:var(--dash-muted);">No returns yet. <a href="{{ route('stock-returns.create') }}" class="text-[var(--dash-brand)]" wire:navigate>Record a return</a> to add stock back to inventory.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($returns->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($returns->previousPageUrl())
                <a href="{{ $returns->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $returns->currentPage() }} of {{ $returns->lastPage() }}</span>
            @if($returns->nextPageUrl())
                <a href="{{ $returns->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
