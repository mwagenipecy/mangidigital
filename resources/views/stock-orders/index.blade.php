@extends('layouts.dashboard')

@section('title', __('Order stock'))

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Order stock</h1>
        <p class="dash-page-subtitle">International or local — track cargo until received and add to inventory</p>
    </div>
    <a href="{{ route('stock-orders.create') }}" class="dash-btn dash-btn-brand" wire:navigate>
        <flux:icon.plus class="size-4" />
        New order
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

{{-- Summary cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
    <div class="dash-card" style="margin-bottom:0;">
        <div style="padding:16px;">
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.02em;">All time</div>
            <div style="font-size:1.5rem;font-weight:700;color:var(--dash-ink);">{{ number_format($overall['count']) }} orders</div>
            <div style="font-size:.85rem;color:var(--dash-muted);margin-top:6px;">Paid: {{ number_format($overall['amount_paid'], 0) }} TZS</div>
            <div style="font-size:.85rem;color:var(--dash-muted);">Expenses: {{ number_format($overall['expenses'], 0) }} TZS</div>
            <div style="font-size:.9rem;font-weight:600;margin-top:8px;color:var(--dash-brand);">Total: {{ number_format($overall['total_cost'], 0) }} TZS</div>
        </div>
    </div>
    <div class="dash-card" style="margin-bottom:0;">
        <div style="padding:16px;">
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.02em;">This month</div>
            <div style="font-size:1.5rem;font-weight:700;color:var(--dash-ink);">{{ number_format($thisMonth['count']) }} orders</div>
            <div style="font-size:.85rem;color:var(--dash-muted);margin-top:6px;">Paid: {{ number_format($thisMonth['amount_paid'], 0) }} TZS</div>
            <div style="font-size:.85rem;color:var(--dash-muted);">Expenses: {{ number_format($thisMonth['expenses'], 0) }} TZS</div>
            <div style="font-size:.9rem;font-weight:600;margin-top:8px;color:var(--dash-brand);">Total: {{ number_format($thisMonth['total_cost'], 0) }} TZS</div>
        </div>
    </div>
    <div class="dash-card" style="margin-bottom:0;">
        <div style="padding:16px;">
            <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:.02em;">This year</div>
            <div style="font-size:1.5rem;font-weight:700;color:var(--dash-ink);">{{ number_format($thisYear['count']) }} orders</div>
            <div style="font-size:.85rem;color:var(--dash-muted);margin-top:6px;">Paid: {{ number_format($thisYear['amount_paid'], 0) }} TZS</div>
            <div style="font-size:.85rem;color:var(--dash-muted);">Expenses: {{ number_format($thisYear['expenses'], 0) }} TZS</div>
            <div style="font-size:.9rem;font-weight:600;margin-top:8px;color:var(--dash-brand);">Total: {{ number_format($thisYear['total_cost'], 0) }} TZS</div>
        </div>
    </div>
</div>

{{-- Monthly & yearly breakdown --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px;margin-bottom:24px;">
    <div class="dash-card" style="margin-bottom:0;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">Monthly summary</div>
                <div class="dash-card-subtitle">Last 12 months — orders, paid & expenses</div>
            </div>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Orders</th>
                        <th>Paid (TZS)</th>
                        <th>Expenses (TZS)</th>
                        <th>Total (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthly as $m)
                    <tr>
                        <td><span class="dash-td-main">{{ $m['label'] }}</span></td>
                        <td><span class="dash-td-sub">{{ $m['count'] }}</span></td>
                        <td><span class="dash-td-sub">{{ number_format($m['amount_paid'], 0) }}</span></td>
                        <td><span class="dash-td-sub">{{ number_format($m['expenses'], 0) }}</span></td>
                        <td><span class="dash-td-amount">{{ number_format($m['total_cost'], 0) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="dash-card" style="margin-bottom:0;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">Yearly summary</div>
                <div class="dash-card-subtitle">By year — orders, paid & expenses</div>
            </div>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Orders</th>
                        <th>Paid (TZS)</th>
                        <th>Expenses (TZS)</th>
                        <th>Total (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($yearly as $y)
                    <tr>
                        <td><span class="dash-td-main">{{ $y['label'] }}</span></td>
                        <td><span class="dash-td-sub">{{ $y['count'] }}</span></td>
                        <td><span class="dash-td-sub">{{ number_format($y['amount_paid'], 0) }}</span></td>
                        <td><span class="dash-td-sub">{{ number_format($y['expenses'], 0) }}</span></td>
                        <td><span class="dash-td-amount">{{ number_format($y['total_cost'], 0) }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:16px;color:var(--dash-muted);">No data yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Stock orders</div>
            <div class="dash-card-subtitle">Payment date, estimated receive, status</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Type</th>
                    <th>Payment date</th>
                    <th>Est. receive</th>
                    <th>Amount / charges</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>
                        <div class="dash-td-main">#{{ $order->id }}</div>
                        <div class="dash-td-sub">{{ $order->serviceProvider?->name ?? '—' }}</div>
                    </td>
                    <td><span class="dash-pill {{ $order->order_type === 'international' ? '' : '' }}" style="font-size:.75rem;">{{ $order->order_type_label }}</span></td>
                    <td><span class="dash-td-sub">{{ $order->payment_date?->format('d M Y') ?? '—' }}</span></td>
                    <td><span class="dash-td-sub">{{ $order->estimated_receive_date?->format('d M Y') ?? '—' }}</span></td>
                    <td><span class="dash-td-sub">{{ number_format($order->amount_paid, 0) }} TZS</span></td>
                    <td>
                        @if($order->status === \App\Models\StockOrder::STATUS_ORDERED)
                            <span class="dash-pill" style="background:#e0f2fe;color:#0369a1;">Ordered</span>
                        @elseif($order->status === \App\Models\StockOrder::STATUS_IN_TRANSIT)
                            <span class="dash-pill" style="background:#fef3c7;color:#b45309;">In transit</span>
                        @else
                            <span class="dash-pill dash-pill-green">Received</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('stock-orders.show', $order) }}" class="dash-btn dash-btn-outline" style="padding:5px 12px;font-size:.75rem;" wire:navigate>View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:24px;color:var(--dash-muted);">No orders yet. <a href="{{ route('stock-orders.create') }}" class="text-[var(--dash-brand)]" wire:navigate>Create a stock order</a> (international or local).</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($orders->previousPageUrl())
                <a href="{{ $orders->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $orders->currentPage() }} of {{ $orders->lastPage() }}</span>
            @if($orders->nextPageUrl())
                <a href="{{ $orders->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
