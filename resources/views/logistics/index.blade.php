@extends('layouts.dashboard')

@section('title', 'Logistics')

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Logistics</h1>
        <p class="dash-page-subtitle">Sales with delivery — products transmitted, transport used, status (arrived / received by customer)</p>
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

{{-- Summary cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:16px;margin-bottom:24px;">
    <div class="dash-card" style="margin-bottom:0;padding:16px;">
        <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;text-transform:uppercase;">Pending</div>
        <div style="font-size:1.75rem;font-weight:700;color:#0369a1;">{{ $summary['pending'] }}</div>
        <div style="font-size:.8rem;color:var(--dash-muted);margin-top:2px;">Not yet dispatched</div>
    </div>
    <div class="dash-card" style="margin-bottom:0;padding:16px;">
        <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;text-transform:uppercase;">In transit</div>
        <div style="font-size:1.75rem;font-weight:700;color:#b45309;">{{ $summary['in_transit'] }}</div>
        <div style="font-size:.8rem;color:var(--dash-muted);margin-top:2px;">On the way</div>
    </div>
    <div class="dash-card" style="margin-bottom:0;padding:16px;">
        <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;text-transform:uppercase;">Arrived</div>
        <div style="font-size:1.75rem;font-weight:700;color:#0d9488;">{{ $summary['arrived'] }}</div>
        <div style="font-size:.8rem;color:var(--dash-muted);margin-top:2px;">Awaiting customer</div>
    </div>
    <div class="dash-card" style="margin-bottom:0;padding:16px;">
        <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;text-transform:uppercase;">Received</div>
        <div style="font-size:1.75rem;font-weight:700;color:#15803d;">{{ $summary['received'] }}</div>
        <div style="font-size:.8rem;color:var(--dash-muted);margin-top:2px;">Customer taken</div>
    </div>
    <div class="dash-card" style="margin-bottom:0;padding:16px;">
        <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;text-transform:uppercase;">Total</div>
        <div style="font-size:1.75rem;font-weight:700;color:var(--dash-ink);">{{ $summary['total'] }}</div>
        <div style="font-size:.8rem;color:var(--dash-muted);margin-top:2px;">Deliveries</div>
    </div>
</div>

<form method="GET" action="{{ route('logistics.index') }}" style="margin-bottom:16px;">
    <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
        <label for="date" style="font-size:.85rem;color:var(--dash-muted);">Date</label>
        <input type="date" id="date" name="date" value="{{ request('date') }}" style="padding:8px 12px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
        <label for="status" style="font-size:.85rem;color:var(--dash-muted);">Status</label>
        <select id="status" name="status" style="padding:8px 12px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            <option value="">All</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>In transit</option>
            <option value="arrived" {{ request('status') === 'arrived' ? 'selected' : '' }}>Arrived</option>
            <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received by customer</option>
        </select>
        <button type="submit" class="dash-btn dash-btn-outline" style="font-size:.85rem;">Filter</button>
    </div>
</form>

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Deliveries</div>
            <div class="dash-card-subtitle">All sales with delivery requested — update status when dispatched, arrived, or customer has taken</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Receipt / Sale</th>
                    <th>Client</th>
                    <th>Products transmitted</th>
                    <th>Transport</th>
                    <th>Status</th>
                    <th>Arrived</th>
                    <th>Customer taken</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveries as $sale)
                <tr>
                    <td><span class="dash-td-sub">{{ $sale->sale_date?->format('d M Y') }}</span></td>
                    <td>
                        <a href="{{ route('sales.show', $sale) }}" class="text-[var(--dash-brand)] font-medium" wire:navigate>{{ $sale->receipt_number ?? '#' . $sale->id }}</a>
                    </td>
                    <td>
                        <div class="dash-td-main">{{ $sale->display_client_name }}</div>
                        <div class="dash-td-sub">{{ $sale->display_client_phone }}</div>
                    </td>
                    <td><span class="dash-td-sub">{{ $sale->items->map(fn ($i) => $i->display_product_name . ' × ' . number_format($i->quantity, 0))->join(', ') ?: '—' }}</span></td>
                    <td><span class="dash-td-sub">{{ $sale->deliveryServiceProvider?->name ?? '—' }}</span></td>
                    <td><span class="dash-pill" style="font-size:.75rem;">{{ $sale->delivery_status_label }}</span></td>
                    <td><span class="dash-td-sub">{{ $sale->delivery_arrived_at?->format('d M H:i') ?? '—' }}</span></td>
                    <td><span class="dash-td-sub">{{ $sale->delivery_received_at ? 'Yes' : '—' }}</span></td>
                    <td>
                        @php $nextStatuses = $sale->allowedNextDeliveryStatuses(); @endphp
                        @if(count($nextStatuses) === 0)
                            <span class="dash-pill dash-pill-green" style="font-size:.75rem;">Received</span>
                        @else
                            <form action="{{ route('logistics.update-status', $sale) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <select name="delivery_status" onchange="this.form.submit()" style="padding:4px 8px;font-size:.8rem;border:1px solid var(--dash-border);border-radius:4px;">
                                    <option value="" disabled>{{ $sale->delivery_status_label }} (current)</option>
                                    @foreach($nextStatuses as $value => $label)
                                        <option value="{{ $value }}">→ {{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:24px;color:var(--dash-muted);">No deliveries. Sales with “Delivery requested” will appear here.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($deliveries->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($deliveries->previousPageUrl())
                <a href="{{ $deliveries->appends(request()->query())->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $deliveries->currentPage() }} of {{ $deliveries->lastPage() }}</span>
            @if($deliveries->nextPageUrl())
                <a href="{{ $deliveries->appends(request()->query())->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
