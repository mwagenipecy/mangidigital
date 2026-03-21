@extends('layouts.dashboard')

@php
    use App\Models\Sale;
    /** @var \App\Models\Sale|\App\Models\CargoShipment $shipment */
    $isSale = $flowType === 'sale';
    $currentStatus = $shipment->delivery_status ?? Sale::DELIVERY_STATUS_PENDING;
    $allowedNext = array_keys($shipment->allowedNextDeliveryStatuses());
    $stages = [
        Sale::DELIVERY_STATUS_PENDING => __('Pending'),
        Sale::DELIVERY_STATUS_IN_TRANSIT => __('In transit'),
        Sale::DELIVERY_STATUS_ARRIVED => __('Arrived'),
        Sale::DELIVERY_STATUS_RECEIVED => __('Received by customer'),
    ];
    $flowTitleRef = $isSale ? ($shipment->receipt_number ?? '#' . $shipment->id) : $shipment->reference_number;
@endphp

@section('title', __('Cargo flow') . ' — ' . $flowTitleRef)

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('logistics.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;">
            <flux:icon.arrow-left class="size-4" />
            {{ __('Back to logistics') }}
        </a>
        <h1 class="dash-page-title">{{ __('Cargo flow') }}</h1>
        <p class="dash-page-subtitle">{{ __('Drag forward only. When moving to Arrived, enter the pickup office — it is emailed to the customer.') }}</p>
    </div>
</div>

<div id="logistics-flow-flash" class="dash-card" style="display:none;margin-bottom:16px;padding:12px 16px;"></div>

@if($shipment->delivery_pickup_office)
<div id="js-flow-pickup-banner" class="dash-card" style="margin-bottom:16px;border-color:#5eead4;background:#f0fdfa;">
    <div style="font-size:.75rem;font-weight:700;color:#0f766e;text-transform:uppercase;margin-bottom:6px;">{{ __('Pickup office') }}</div>
    <div id="js-flow-pickup-text" style="font-size:.95rem;color:#134e4a;white-space:pre-wrap;">{{ $shipment->delivery_pickup_office }}</div>
</div>
@else
<div id="js-flow-pickup-banner" class="dash-card" style="display:none;margin-bottom:16px;border-color:#5eead4;background:#f0fdfa;">
    <div style="font-size:.75rem;font-weight:700;color:#0f766e;text-transform:uppercase;margin-bottom:6px;">{{ __('Pickup office') }}</div>
    <div id="js-flow-pickup-text" style="font-size:.95rem;color:#134e4a;white-space:pre-wrap;"></div>
</div>
@endif

<div class="dash-card" style="margin-bottom:16px;">
    <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-start;justify-content:space-between;">
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);text-transform:uppercase;">{{ $isSale ? __('Receipt') : __('Reference') }}</div>
            <div style="font-weight:700;font-size:1.1rem;">
                @if($isSale)
                    <a href="{{ route('sales.show', $shipment) }}" class="text-[var(--dash-brand)]" wire:navigate>{{ $shipment->receipt_number ?? '#' . $shipment->id }}</a>
                @else
                    <span>{{ $shipment->reference_number }}</span>
                    <span class="dash-pill" style="font-size:.65rem;margin-left:8px;">{{ __('Custom cargo') }}</span>
                @endif
            </div>
        </div>
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);text-transform:uppercase;">{{ __('Client') }}</div>
            <div class="dash-td-main">{{ $isSale ? $shipment->display_client_name : $shipment->client_name }}</div>
            <div class="dash-td-sub">{{ $isSale ? $shipment->display_client_phone : $shipment->client_phone }}</div>
        </div>
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);text-transform:uppercase;">{{ __('Transport') }}</div>
            <div>{{ $shipment->deliveryServiceProvider?->name ?? '—' }}</div>
        </div>
        <div>
            <div style="font-size:.75rem;color:var(--dash-muted);text-transform:uppercase;">{{ __('Delivery cost') }}</div>
            <div>{{ number_format((float) ($shipment->delivery_cost ?? 0), 0) }} TZS</div>
        </div>
    </div>
    <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--dash-border);">
        <div style="font-size:.75rem;color:var(--dash-muted);margin-bottom:4px;">{{ $isSale ? __('Products') : __('Cargo') }}</div>
        <div style="font-size:.9rem;">
            @if($isSale)
                {{ $shipment->items->map(fn ($i) => $i->display_product_name . ' × ' . number_format($i->quantity, 0))->join(', ') ?: '—' }}
            @else
                {{ $shipment->cargo_description ?: '—' }}
            @endif
        </div>
    </div>
</div>

<div class="logistics-flow-board" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;align-items:stretch;">
    @foreach($stages as $status => $label)
        <div
            class="logistics-flow-column dash-card"
            data-stage="{{ $status }}"
            style="margin-bottom:0;min-height:280px;display:flex;flex-direction:column;padding:0;overflow:hidden;"
        >
            <div style="padding:12px 14px;border-bottom:1px solid var(--dash-border);background:var(--dash-surface-2,#f8fafb);">
                <div style="font-weight:700;font-size:.9rem;">{{ $label }}</div>
                @if($status === Sale::DELIVERY_STATUS_PENDING)
                    <div style="font-size:.75rem;color:var(--dash-muted);margin-top:2px;">{{ __('Awaiting dispatch') }}</div>
                @elseif($status === Sale::DELIVERY_STATUS_IN_TRANSIT)
                    <div style="font-size:.75rem;color:var(--dash-muted);margin-top:2px;">{{ __('On the way') }}</div>
                @elseif($status === Sale::DELIVERY_STATUS_ARRIVED)
                    <div style="font-size:.75rem;color:var(--dash-muted);margin-top:2px;">{{ __('At destination') }}</div>
                @else
                    <div style="font-size:.75rem;color:var(--dash-muted);margin-top:2px;">{{ __('Completed') }}</div>
                @endif
            </div>
            <div
                class="js-flow-dropzone logistics-flow-dropzone"
                data-status="{{ $status }}"
                style="flex:1;padding:12px;min-height:200px;transition:background .15s,border-color .15s;border:2px dashed transparent;border-radius:0 0 var(--dash-r-sm) var(--dash-r-sm);"
            >
                @if($currentStatus === $status)
                    <div
                        id="logistics-cargo-card"
                        class="logistics-cargo-card"
                        draggable="{{ count($allowedNext) > 0 ? 'true' : 'false' }}"
                        data-current-status="{{ $currentStatus }}"
                        style="background:#fff;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);padding:14px;cursor:{{ count($allowedNext) > 0 ? 'grab' : 'default' }};box-shadow:0 2px 8px rgba(30,58,68,.06);"
                    >
                        <div style="font-size:.7rem;color:var(--dash-muted);text-transform:uppercase;margin-bottom:6px;">{{ __('Shipment') }}</div>
                        <div style="font-weight:700;margin-bottom:4px;">{{ $flowTitleRef }}</div>
                        <div style="font-size:.85rem;color:var(--dash-muted);">{{ $isSale ? $shipment->display_client_name : $shipment->client_name }}</div>
                        @if(count($allowedNext) > 0)
                            <div class="logistics-cargo-card-hint" style="margin-top:12px;font-size:.75rem;color:var(--dash-brand);font-weight:600;">{{ __('Drag to a green column →') }}</div>
                        @else
                            <div class="logistics-cargo-card-hint" style="margin-top:12px;font-size:.75rem;color:var(--dash-muted);">{{ __('Final stage — no further moves') }}</div>
                        @endif
                    </div>
                @else
                    <div class="js-flow-placeholder" style="font-size:.8rem;color:var(--dash-muted);padding:8px 4px;">
                        @if(in_array($status, $allowedNext, true))
                            {{ __('Drop here to advance') }}
                        @else
                            —
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>

<p style="margin-top:16px;font-size:.85rem;color:var(--dash-muted);max-width:720px;">
    {{ __('Arrived requires a pickup office (address or branch name). It is included in the customer email and on the public tracking page.') }}
</p>

<div id="js-flow-advance-wrap" class="dash-card" style="margin-top:16px;{{ count($allowedNext) ? '' : 'display:none;' }}">
    <div class="dash-card-title" style="font-size:.95rem;margin-bottom:8px;">{{ __('Or use buttons') }}</div>
    <div id="js-flow-advance-buttons" style="display:flex;flex-wrap:wrap;gap:8px;">
        @foreach($shipment->allowedNextDeliveryStatuses() as $value => $label)
            <button type="button" class="dash-btn dash-btn-outline js-flow-advance-btn" data-status="{{ $value }}">{{ __('Move to') }}: {{ $label }}</button>
        @endforeach
    </div>
</div>

{{-- Pickup office modal (Arrived) --}}
<div id="pickup-office-modal" class="hidden fixed inset-0 z-[300] flex items-center justify-center p-4" style="background:rgba(15,23,42,.45);" aria-hidden="true">
    <div class="dash-card" style="max-width:420px;width:100%;margin:0;position:relative;">
        <h3 class="dash-card-title" style="font-size:1rem;margin-bottom:8px;">{{ __('Pickup office / address') }}</h3>
        <p style="font-size:.85rem;color:var(--dash-muted);margin:0 0 12px;">{{ __('Where should the customer collect the cargo? This will be emailed and shown on tracking.') }}</p>
        <textarea id="pickup-office-input" rows="4" class="dash-input" style="width:100%;padding:10px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;resize:vertical;" placeholder="{{ __('e.g. Mangi Digital — Kimara office, Plot 12, ...') }}"></textarea>
        <p id="pickup-office-modal-error" style="display:none;margin:8px 0 0;font-size:.8rem;color:var(--dash-danger);"></p>
        <div style="display:flex;gap:10px;margin-top:16px;justify-content:flex-end;">
            <button type="button" id="pickup-office-cancel" class="dash-btn dash-btn-outline">{{ __('Cancel') }}</button>
            <button type="button" id="pickup-office-confirm" class="dash-btn dash-btn-brand">{{ __('Confirm') }}</button>
        </div>
    </div>
</div>

<script>
(function() {
    const STATUS_ARRIVED = @json(\App\Models\Sale::DELIVERY_STATUS_ARRIVED);
    const card = document.getElementById('logistics-cargo-card');
    const updateUrl = @json(route('logistics.update-status', ['flow_token' => $shipment->logistics_flow_token]));
    const csrf = @json(csrf_token());
    let allowedNext = @json($allowedNext);
    const flashEl = document.getElementById('logistics-flow-flash');
    const pickupModal = document.getElementById('pickup-office-modal');
    const pickupInput = document.getElementById('pickup-office-input');
    const pickupModalErr = document.getElementById('pickup-office-modal-error');
    const pickupBanner = document.getElementById('js-flow-pickup-banner');
    const pickupTextEl = document.getElementById('js-flow-pickup-text');

    let pickupResolve = null;

    function showFlash(msg, isError) {
        if (!flashEl) return;
        flashEl.style.display = 'block';
        flashEl.style.background = isError ? 'rgba(239,68,68,.08)' : 'var(--dash-brand-10)';
        flashEl.style.borderColor = isError ? 'var(--dash-danger)' : 'var(--dash-brand)';
        flashEl.style.color = isError ? 'var(--dash-danger)' : 'var(--dash-ink)';
        flashEl.textContent = msg;
        if (!isError) setTimeout(function() { flashEl.style.display = 'none'; }, 3500);
    }

    function openPickupModal() {
        return new Promise(function(resolve) {
            pickupResolve = resolve;
            if (pickupInput) pickupInput.value = '';
            if (pickupModalErr) { pickupModalErr.style.display = 'none'; pickupModalErr.textContent = ''; }
            if (pickupModal) {
                pickupModal.classList.remove('hidden');
                pickupModal.setAttribute('aria-hidden', 'false');
            }
            document.body.classList.add('overflow-hidden');
        });
    }

    function closePickupModal(result) {
        if (pickupModal) {
            pickupModal.classList.add('hidden');
            pickupModal.setAttribute('aria-hidden', 'true');
        }
        document.body.classList.remove('overflow-hidden');
        if (pickupResolve) {
            var r = pickupResolve;
            pickupResolve = null;
            r(result);
        }
    }

    document.getElementById('pickup-office-cancel')?.addEventListener('click', function() {
        closePickupModal(false);
    });
    document.getElementById('pickup-office-confirm')?.addEventListener('click', function() {
        var v = (pickupInput && pickupInput.value) ? pickupInput.value.trim() : '';
        if (v.length < 3) {
            if (pickupModalErr) {
                pickupModalErr.textContent = @json(__('Please enter at least a few characters for the pickup location.'));
                pickupModalErr.style.display = 'block';
            }
            return;
        }
        closePickupModal(v);
    });

    function columnForStatus(status) {
        return document.querySelector('.logistics-flow-column[data-stage="' + status + '"]');
    }

    function dropzoneForStatus(status) {
        var col = columnForStatus(status);
        return col ? col.querySelector('.js-flow-dropzone') : null;
    }

    function refreshPlaceholders() {
        document.querySelectorAll('.js-flow-dropzone').forEach(function(zone) {
            var st = zone.getAttribute('data-status');
            if (card && zone.contains(card)) {
                var phCard = zone.querySelector('.js-flow-placeholder');
                if (phCard) phCard.remove();
                return;
            }
            var ph = zone.querySelector('.js-flow-placeholder');
            if (!ph) {
                ph = document.createElement('div');
                ph.className = 'js-flow-placeholder';
                ph.style.cssText = 'font-size:.8rem;color:var(--dash-muted);padding:8px 4px;';
                zone.appendChild(ph);
            }
            if (allowedNext.indexOf(st) !== -1) {
                ph.textContent = @json(__('Drop here to advance'));
            } else {
                ph.textContent = '—';
            }
        });
    }

    var statusLabels = {
        pending: @json(__('Pending')),
        in_transit: @json(__('In transit')),
        arrived: @json(__('Arrived')),
        received: @json(__('Received by customer')),
    };

    function renderAdvanceButtons() {
        var wrap = document.getElementById('js-flow-advance-wrap');
        var container = document.getElementById('js-flow-advance-buttons');
        if (!wrap || !container) return;
        if (!allowedNext.length) {
            wrap.style.display = 'none';
            container.innerHTML = '';
            return;
        }
        wrap.style.display = 'block';
        container.innerHTML = '';
        allowedNext.forEach(function(st) {
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'dash-btn dash-btn-outline js-flow-advance-btn';
            b.setAttribute('data-status', st);
            b.textContent = @json(__('Move to')) + ': ' + (statusLabels[st] || st);
            container.appendChild(b);
        });
    }

    function isDropAllowed(targetStatus) {
        return allowedNext.indexOf(targetStatus) !== -1;
    }

    function paintDropzones(active) {
        document.querySelectorAll('.js-flow-dropzone').forEach(function(zone) {
            var st = zone.getAttribute('data-status');
            var ok = active && isDropAllowed(st);
            zone.style.background = ok ? 'rgba(34,197,94,.12)' : '';
            zone.style.borderColor = ok ? '#22c55e' : 'transparent';
        });
    }

    function updatePickupBanner(text) {
        if (!text || !pickupBanner || !pickupTextEl) return;
        pickupTextEl.textContent = text;
        pickupBanner.style.display = 'block';
    }

    async function advanceTo(targetStatus) {
        if (!isDropAllowed(targetStatus)) {
            showFlash(@json(__('You cannot move backward or skip invalid steps.')), true);
            return;
        }
        var pickupOffice = null;
        if (targetStatus === STATUS_ARRIVED) {
            pickupOffice = await openPickupModal();
            if (pickupOffice === false) return;
        }
        var body = { delivery_status: targetStatus };
        if (pickupOffice) body.delivery_pickup_office = pickupOffice;
        try {
            var res = await fetch(updateUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body),
            });
            var data = await res.json().catch(function() { return {}; });
            if (!res.ok) {
                var errMsg = data.message || @json(__('Update failed.'));
                if (data.errors && data.errors.delivery_pickup_office) {
                    errMsg = data.errors.delivery_pickup_office[0];
                }
                showFlash(errMsg, true);
                return;
            }
            allowedNext = data.allowed_next || [];
            var newStatus = data.delivery_status;
            if (data.delivery_pickup_office) {
                updatePickupBanner(data.delivery_pickup_office);
            }
            if (card) {
                card.setAttribute('data-current-status', newStatus);
                card.draggable = allowedNext.length > 0;
                card.style.cursor = allowedNext.length > 0 ? 'grab' : 'default';
                var hint = card.querySelector('.logistics-cargo-card-hint');
                if (hint) {
                    hint.textContent = allowedNext.length > 0
                        ? @json(__('Drag to a green column →'))
                        : @json(__('Final stage — no further moves'));
                    hint.style.color = allowedNext.length > 0 ? 'var(--dash-brand)' : 'var(--dash-muted)';
                }
            }
            var targetZone = dropzoneForStatus(newStatus);
            if (card && targetZone) {
                targetZone.appendChild(card);
            }
            refreshPlaceholders();
            renderAdvanceButtons();
            showFlash(data.message || @json(__('Delivery status updated.')), false);
        } catch (e) {
            showFlash(@json(__('Network error.')), true);
        }
    }

    if (card && allowedNext.length > 0) {
        card.addEventListener('dragstart', function(e) {
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', card.getAttribute('data-current-status') || '');
            card.style.opacity = '0.85';
            paintDropzones(true);
        });
        card.addEventListener('dragend', function() {
            card.style.opacity = '1';
            paintDropzones(false);
        });
    }

    document.querySelectorAll('.js-flow-dropzone').forEach(function(zone) {
        zone.addEventListener('dragover', function(e) {
            var st = zone.getAttribute('data-status');
            if (!card || !isDropAllowed(st)) return;
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        });
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            paintDropzones(false);
            var st = zone.getAttribute('data-status');
            if (!isDropAllowed(st)) {
                showFlash(@json(__('You can only move forward to allowed stages.')), true);
                return;
            }
            advanceTo(st);
        });
    });

    var advanceWrap = document.getElementById('js-flow-advance-wrap');
    if (advanceWrap) {
        advanceWrap.addEventListener('click', function(e) {
            var btn = e.target.closest('.js-flow-advance-btn');
            if (!btn) return;
            advanceTo(btn.getAttribute('data-status'));
        });
    }

    refreshPlaceholders();
})();
</script>
<style>
@media (max-width: 900px) {
    .logistics-flow-board { grid-template-columns: 1fr !important; }
}
</style>
@endsection
