@extends('layouts.dashboard')

@section('title', 'Sale ' . ($sale->receipt_number ?? '#' . $sale->id))

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('sales.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            Back to sales
        </a>
        <h1 class="dash-page-title">{{ $sale->receipt_number ?? 'Sale #' . $sale->id }}</h1>
        <p class="dash-page-subtitle">{{ $sale->sale_date?->format('d M Y') }} — {{ $sale->display_client_name }}</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('sales.receipt', $sale) }}" target="_blank" class="dash-btn dash-btn-outline" style="display:inline-flex;align-items:center;gap:6px;">
            <flux:icon.arrow-down-tray class="size-4" />
            Download / Print receipt
        </a>
        <button type="button" onclick="window.open('{{ route('sales.receipt', $sale) }}','_blank','width=800,height=700'); window.focus(); setTimeout(function(){ window.print(); }, 500);" class="dash-btn dash-btn-brand">
            <flux:icon.printer class="size-4" style="margin-right:6px;" />
            Print receipt
        </button>
    </div>
</div>

@if(session('success'))
    <div class="dash-card" style="margin-bottom:16px;background:var(--dash-brand-10);border-color:var(--dash-brand);">
        <p style="margin:0;font-size:.9rem;color:var(--dash-ink);">{{ session('success') }}</p>
    </div>
@endif

<div class="dash-card" style="margin-bottom:20px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Sale details</div>
            <div class="dash-card-subtitle">Client, date, totals</div>
        </div>
    </div>
    <div style="padding:0 20px 20px;display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;">
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:2px;">Client</div>
            <div style="font-weight:600;">{{ $sale->display_client_name }}</div>
            <div style="font-size:.85rem;color:var(--dash-muted);">{{ $sale->display_client_phone }}</div>
        </div>
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:2px;">Date</div>
            <div style="font-weight:600;">{{ $sale->sale_date?->format('d M Y') }}</div>
        </div>
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:2px;">Subtotal (TZS)</div>
            <div style="font-weight:600;">{{ number_format($sale->subtotal, 0) }}</div>
        </div>
        @if($sale->delivery_requested)
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:2px;">Delivery</div>
            <div style="font-weight:600;">{{ number_format($sale->delivery_cost, 0) }} TZS</div>
            @if($sale->deliveryServiceProvider)
                <div style="font-size:.85rem;color:var(--dash-muted);">{{ $sale->deliveryServiceProvider->name }}</div>
            @endif
        </div>
        @endif
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:2px;">Total (TZS)</div>
            <div style="font-weight:700;font-size:1.1rem;color:var(--dash-brand);">{{ number_format($sale->total, 0) }}</div>
        </div>
    </div>
    @if($sale->notes)
        <div style="padding:0 20px 20px;border-top:1px solid var(--dash-border);">
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;">Notes</div>
            <p style="margin:0;font-size:.9rem;">{{ $sale->notes }}</p>
        </div>
    @endif
</div>

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Items</div>
            <div class="dash-card-subtitle">Product, store, quantity, price</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Store</th>
                    <th>Quantity</th>
                    <th>Unit price (TZS)</th>
                    <th>Total (TZS)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td><span class="dash-td-main">{{ $item->display_product_name }}</span></td>
                    <td><span class="dash-td-sub">{{ $item->store?->name ?? '—' }}</span></td>
                    <td><span class="dash-td-sub">{{ number_format($item->quantity, 0) }}</span></td>
                    <td><span class="dash-td-sub">{{ number_format($item->unit_price, 0) }}</span></td>
                    <td><span class="dash-td-amount">{{ number_format($item->line_total, 0) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
