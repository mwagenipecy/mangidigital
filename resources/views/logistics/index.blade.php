@extends('layouts.dashboard')

@section('title', 'Logistics')

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Logistics</h1>
        <p class="dash-page-subtitle">{{ __('Sale deliveries and custom cargo — same flow board and customer tracking') }}</p>
    </div>
    <a href="{{ route('logistics.cargo.create') }}" class="dash-btn dash-btn-brand" wire:navigate>
        {{ __('Add custom cargo') }}
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
        <div style="font-size:.8rem;color:var(--dash-muted);margin-top:2px;">All shipments</div>
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

<div class="dash-card" style="margin-bottom:20px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">{{ __('Deliveries from sales') }}</div>
            <div class="dash-card-subtitle">{{ __('Sales with delivery requested') }}</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Receipt / Sale</th>
                    <th>Client</th>
                    <th>Products</th>
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
                    <td style="white-space:nowrap;">
                        @if($sale->logistics_flow_token)
                            <a href="{{ route('logistics.flow', ['flow_token' => $sale->logistics_flow_token]) }}" target="_blank" rel="noopener noreferrer" class="dash-btn dash-btn-outline" style="padding:5px 10px;font-size:.75rem;margin-right:6px;">{{ __('View flow') }}</a>
                        @endif
                        @php $nextStatuses = $sale->allowedNextDeliveryStatuses(); @endphp
                        @if(count($nextStatuses) > 0 && $sale->logistics_flow_token)
                            <form action="{{ route('logistics.update-status', ['flow_token' => $sale->logistics_flow_token]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="delivery_pickup_office" value="">
                                <select name="delivery_status" onchange="if(this.options[this.selectedIndex].dataset.needPickup === '1') { var p = prompt(@json(__('Pickup office / address (required for Arrived)'))); if(p === null) { this.value=''; return; } this.form.querySelector('input[name=delivery_pickup_office]').value = p || ''; } this.form.submit();" style="padding:4px 8px;font-size:.8rem;border:1px solid var(--dash-border);border-radius:4px;max-width:140px;">
                                    <option value="" disabled selected>{{ $sale->delivery_status_label }}</option>
                                    @foreach($nextStatuses as $value => $label)
                                        <option value="{{ $value }}" data-need-pickup="{{ $value === \App\Models\Sale::DELIVERY_STATUS_ARRIVED ? '1' : '0' }}">→ {{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @elseif(count($nextStatuses) === 0)
                            <span class="dash-pill dash-pill-green" style="font-size:.75rem;">{{ __('Done') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:24px;color:var(--dash-muted);">{{ __('No sale deliveries for this filter.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($deliveries->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($deliveries->previousPageUrl())
                <a href="{{ $deliveries->appends(request()->except('page'))->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $deliveries->currentPage() }} of {{ $deliveries->lastPage() }}</span>
            @if($deliveries->nextPageUrl())
                <a href="{{ $deliveries->appends(request()->except('page'))->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">{{ __('Custom cargo') }}</div>
            <div class="dash-card-subtitle">{{ __('Not linked to a sale — use “Add custom cargo” to create') }}</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>{{ __('Reference') }}</th>
                    <th>{{ __('Created') }}</th>
                    <th>{{ __('Client') }}</th>
                    <th>{{ __('Cargo') }}</th>
                    <th>{{ __('Transport') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cargoShipments as $cargo)
                <tr>
                    <td><span class="dash-td-main">{{ $cargo->reference_number }}</span></td>
                    <td><span class="dash-td-sub">{{ $cargo->created_at?->format('d M Y') }}</span></td>
                    <td>
                        <div class="dash-td-main">{{ $cargo->client_name }}</div>
                        <div class="dash-td-sub">{{ $cargo->client_phone }}</div>
                    </td>
                    <td><span class="dash-td-sub">{{ \Illuminate\Support\Str::limit($cargo->cargo_description ?? '—', 48) }}</span></td>
                    <td><span class="dash-td-sub">{{ $cargo->deliveryServiceProvider?->name ?? '—' }}</span></td>
                    <td><span class="dash-pill" style="font-size:.75rem;">{{ $cargo->delivery_status_label }}</span></td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('logistics.flow', ['flow_token' => $cargo->logistics_flow_token]) }}" target="_blank" rel="noopener noreferrer" class="dash-btn dash-btn-outline" style="padding:5px 10px;font-size:.75rem;margin-right:6px;">{{ __('View flow') }}</a>
                        @php $nextC = $cargo->allowedNextDeliveryStatuses(); @endphp
                        @if(count($nextC) > 0)
                            <form action="{{ route('logistics.update-status', ['flow_token' => $cargo->logistics_flow_token]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="delivery_pickup_office" value="">
                                <select name="delivery_status" onchange="if(this.options[this.selectedIndex].dataset.needPickup === '1') { var p = prompt(@json(__('Pickup office / address (required for Arrived)'))); if(p === null) { this.value=''; return; } this.form.querySelector('input[name=delivery_pickup_office]').value = p || ''; } this.form.submit();" style="padding:4px 8px;font-size:.8rem;border:1px solid var(--dash-border);border-radius:4px;max-width:140px;">
                                    <option value="" disabled selected>{{ $cargo->delivery_status_label }}</option>
                                    @foreach($nextC as $value => $label)
                                        <option value="{{ $value }}" data-need-pickup="{{ $value === \App\Models\CargoShipment::DELIVERY_STATUS_ARRIVED ? '1' : '0' }}">→ {{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @else
                            <span class="dash-pill dash-pill-green" style="font-size:.75rem;">{{ __('Done') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:24px;color:var(--dash-muted);">{{ __('No custom cargo yet.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($cargoShipments->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($cargoShipments->previousPageUrl())
                <a href="{{ $cargoShipments->appends(request()->except('cargo_page'))->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>&larr; Previous</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">Page {{ $cargoShipments->currentPage() }} of {{ $cargoShipments->lastPage() }}</span>
            @if($cargoShipments->nextPageUrl())
                <a href="{{ $cargoShipments->appends(request()->except('cargo_page'))->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>Next &rarr;</a>
            @endif
        </div>
    @endif
</div>
@endsection
