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
    $pickupOfficeRaw = $shipment->delivery_pickup_office ?? '';
    $hasPickupOffice = is_string($pickupOfficeRaw) && strlen(trim($pickupOfficeRaw)) > 0;
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
        <p class="dash-page-subtitle">{{ __('Drag forward only. When you move to Arrived, a dialog opens so you can add pickup details (optional).') }}</p>
    </div>
</div>

<div id="logistics-flow-flash" class="dash-card" style="display:none;margin-bottom:16px;padding:12px 16px;"></div>

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

{{-- Pickup location: after shipment context so it reads naturally --}}
<div id="js-flow-pickup-banner" class="logistics-pickup-spotlight @if($hasPickupOffice) is-visible @endif" aria-live="polite">
    <div class="logistics-pickup-spotlight__rail" aria-hidden="true"></div>
    <div class="logistics-pickup-spotlight__inner">
        <div class="logistics-pickup-spotlight__icon-wrap" aria-hidden="true">
            <flux:icon.map-pin class="size-6 shrink-0 text-teal-700" />
        </div>
        <div class="logistics-pickup-spotlight__meta">
            <div class="logistics-pickup-spotlight__badge">
                <flux:icon.building-office-2 class="size-3.5 shrink-0" />
                {{ __('Customer pickup') }}
            </div>
            <h2 class="logistics-pickup-spotlight__title">{{ __('Where to collect this cargo') }}</h2>
            <p class="logistics-pickup-spotlight__hint">{{ __('This address is emailed to the customer and shown on the public tracking page.') }}</p>
            <div class="logistics-pickup-spotlight__address-wrap">
                <div id="js-flow-pickup-text" class="logistics-pickup-spotlight__address">{{ $hasPickupOffice ? $pickupOfficeRaw : '' }}</div>
                <button type="button" id="js-flow-pickup-copy" class="logistics-pickup-spotlight__copy dash-btn dash-btn-outline" title="{{ __('Copy address') }}">
                    <flux:icon.clipboard-document-list class="size-4 shrink-0" />
                    <span>{{ __('Copy') }}</span>
                </button>
            </div>
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
    {{ __('Moving to Arrived opens a dialog. Pickup office or address is optional; if you add it, it is included in the customer email and on tracking.') }}
</p>

<div id="js-flow-advance-wrap" class="dash-card" style="margin-top:16px;{{ count($allowedNext) ? '' : 'display:none;' }}">
    <div class="dash-card-title" style="font-size:.95rem;margin-bottom:8px;">{{ __('Or use buttons') }}</div>
    <div id="js-flow-advance-buttons" style="display:flex;flex-wrap:wrap;gap:8px;">
        @foreach($shipment->allowedNextDeliveryStatuses() as $value => $label)
            <button type="button" class="dash-btn dash-btn-outline js-flow-advance-btn" data-status="{{ $value }}">{{ __('Move to') }}: {{ $label }}</button>
        @endforeach
    </div>
</div>

{{-- Pickup modal: portaled-style fixed overlay (must not use Tailwind "relative" on same node as "fixed" — breaks viewport lock; "hidden"+"flex" can leave modal visible in flow) --}}
<div id="pickup-office-modal" class="logistics-pickup-modal-root" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="pickup-office-modal-title">
    <div id="pickup-office-modal-backdrop" class="logistics-pickup-modal__backdrop" aria-hidden="true"></div>
    <div class="logistics-pickup-modal__panel dash-card" onclick="event.stopPropagation();">
        <div class="logistics-pickup-modal__hero">
            <div class="logistics-pickup-modal__hero-icon" aria-hidden="true">
                <flux:icon.map-pin class="size-6 text-teal-800" />
            </div>
            <div>
                <h3 id="pickup-office-modal-title" class="logistics-pickup-modal__hero-title">{{ __('Mark as arrived') }}</h3>
                <p class="logistics-pickup-modal__hero-sub">{{ __('Add where the customer can collect the cargo if you know it — or continue without. Either way the status updates and the customer is notified.') }}</p>
            </div>
        </div>
        <div class="logistics-pickup-modal__body">
            <label for="pickup-office-input" class="logistics-pickup-modal__label">{{ __('Pickup office / address') }} <span class="logistics-pickup-modal__optional">({{ __('optional') }})</span></label>
            <p class="logistics-pickup-modal__field-hint">{{ __('Shown in email and tracking when provided.') }}</p>
            <textarea id="pickup-office-input" rows="5" class="logistics-pickup-modal__textarea dash-input" placeholder="{{ __('e.g. Mangi Digital — Kimara office, Plot 12, Open Mon–Sat 9:00–18:00') }}" autocomplete="street-address"></textarea>
            <p id="pickup-office-modal-error" class="logistics-pickup-modal__error" style="display:none;"></p>
            <div class="logistics-pickup-modal__actions logistics-pickup-modal__actions--split">
                <button type="button" id="pickup-office-cancel" class="dash-btn dash-btn-outline">{{ __('Cancel') }}</button>
                <div class="logistics-pickup-modal__actions-primary">
                    <button type="button" id="pickup-office-skip" class="dash-btn dash-btn-outline">{{ __('No pickup details') }}</button>
                    <button type="button" id="pickup-office-confirm" class="dash-btn dash-btn-brand">{{ __('Mark as arrived') }}</button>
                </div>
            </div>
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

    function onPickupModalKeydown(e) {
        if (e.key === 'Escape') {
            e.preventDefault();
            closePickupModal(false);
        }
    }

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
                pickupModal.classList.add('is-open');
                pickupModal.setAttribute('aria-hidden', 'false');
            }
            document.body.classList.add('overflow-hidden');
            document.addEventListener('keydown', onPickupModalKeydown);
            setTimeout(function() { if (pickupInput) pickupInput.focus(); }, 60);
        });
    }

    function closePickupModal(result) {
        document.removeEventListener('keydown', onPickupModalKeydown);
        if (pickupModal) {
            pickupModal.classList.remove('is-open');
            pickupModal.setAttribute('aria-hidden', 'true');
        }
        document.body.classList.remove('overflow-hidden');
        if (pickupResolve) {
            var r = pickupResolve;
            pickupResolve = null;
            r(result);
        }
    }

    document.getElementById('pickup-office-modal-backdrop')?.addEventListener('click', function() {
        closePickupModal(false);
    });

    document.getElementById('pickup-office-cancel')?.addEventListener('click', function() {
        closePickupModal(false);
    });

    document.getElementById('pickup-office-skip')?.addEventListener('click', function() {
        if (pickupModalErr) { pickupModalErr.style.display = 'none'; pickupModalErr.textContent = ''; }
        closePickupModal(null);
    });

    document.getElementById('pickup-office-confirm')?.addEventListener('click', function() {
        if (pickupModalErr) { pickupModalErr.style.display = 'none'; pickupModalErr.textContent = ''; }
        var raw = pickupInput ? pickupInput.value.trim() : '';
        if (raw.length > 5000) {
            if (pickupModalErr) {
                pickupModalErr.textContent = @json(__('Pickup details are too long (max 5000 characters).'));
                pickupModalErr.style.display = 'block';
            }
            return;
        }
        closePickupModal(raw === '' ? null : raw);
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
        pickupBanner.classList.add('is-visible');
    }

    function syncPickupBannerFromResponse(pickup) {
        if (!pickupBanner || !pickupTextEl) return;
        var t = pickup != null ? String(pickup).trim() : '';
        if (t) {
            pickupTextEl.textContent = t;
            pickupBanner.classList.add('is-visible');
        } else {
            pickupTextEl.textContent = '';
            pickupBanner.classList.remove('is-visible');
        }
    }

    document.getElementById('js-flow-pickup-copy')?.addEventListener('click', function() {
        var t = pickupTextEl && pickupTextEl.textContent ? pickupTextEl.textContent.trim() : '';
        if (!t) return;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(t).then(function() {
                showFlash(@json(__('Address copied to clipboard.')), false);
            }).catch(function() {
                showFlash(@json(__('Could not copy. Select the text manually.')), true);
            });
        } else {
            showFlash(@json(__('Could not copy. Select the text manually.')), true);
        }
    });

    async function advanceTo(targetStatus) {
        if (!isDropAllowed(targetStatus)) {
            showFlash(@json(__('You cannot move backward or skip invalid steps.')), true);
            return;
        }
        var pickupOffice = undefined;
        if (targetStatus === STATUS_ARRIVED) {
            pickupOffice = await openPickupModal();
            if (pickupOffice === false) return;
        }
        var body = { delivery_status: targetStatus };
        if (targetStatus === STATUS_ARRIVED) {
            body.delivery_pickup_office = pickupOffice != null && pickupOffice !== '' ? pickupOffice : null;
        }
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
            syncPickupBannerFromResponse(data.delivery_pickup_office);
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
/* Pickup spotlight (saved pickup location) */
.logistics-pickup-spotlight {
    display: none;
    margin-bottom: 20px;
    border-radius: 14px;
    border: 1px solid rgba(13, 148, 136, 0.22);
    background: linear-gradient(125deg, rgba(236, 253, 245, 0.97) 0%, rgba(255, 255, 255, 0.92) 42%, rgba(204, 251, 241, 0.55) 100%);
    box-shadow:
        0 1px 2px rgba(15, 23, 42, 0.04),
        0 12px 40px -12px rgba(13, 148, 136, 0.18);
    overflow: hidden;
    align-items: stretch;
}
.logistics-pickup-spotlight.is-visible {
    display: flex;
}
.logistics-pickup-spotlight__rail {
    width: 5px;
    flex-shrink: 0;
    background: linear-gradient(180deg, #2dd4bf, #0d9488 55%, #0f766e);
}
.logistics-pickup-spotlight__inner {
    display: flex;
    flex-wrap: wrap;
    gap: 18px 24px;
    padding: 20px 22px;
    align-items: flex-start;
    flex: 1;
    min-width: 0;
}
.logistics-pickup-spotlight__icon-wrap {
    flex-shrink: 0;
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(13, 148, 136, 0.18);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0f766e;
    box-shadow: 0 2px 8px rgba(13, 148, 136, 0.08);
}
.logistics-pickup-spotlight__meta {
    flex: 1;
    min-width: min(100%, 220px);
}
.logistics-pickup-spotlight__badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.6875rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #0f766e;
    background: rgba(255, 255, 255, 0.75);
    border: 1px solid rgba(13, 148, 136, 0.2);
    padding: 5px 11px;
    border-radius: 999px;
    margin-bottom: 10px;
}
.logistics-pickup-spotlight__title {
    margin: 0 0 6px;
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--dash-ink, #0f172a);
    letter-spacing: -0.02em;
    line-height: 1.25;
}
.logistics-pickup-spotlight__hint {
    margin: 0 0 14px;
    font-size: 0.8125rem;
    line-height: 1.5;
    color: var(--dash-muted, #64748b);
    max-width: 36rem;
}
.logistics-pickup-spotlight__address-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: flex-start;
}
.logistics-pickup-spotlight__address {
    flex: 1;
    min-width: min(100%, 200px);
    font-size: 0.9375rem;
    line-height: 1.55;
    color: #134e4a;
    white-space: pre-wrap;
    word-break: break-word;
    padding: 14px 16px;
    background: rgba(255, 255, 255, 0.72);
    border: 1px solid rgba(13, 148, 136, 0.14);
    border-radius: 10px;
}
.logistics-pickup-spotlight__copy {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    font-size: 0.8125rem;
    flex-shrink: 0;
}

/* Pickup modal — viewport-fixed overlay (above dash sidebar 200 / overlay 450) */
.logistics-pickup-modal-root {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 2100;
    margin: 0;
    padding: max(16px, env(safe-area-inset-top)) max(16px, env(safe-area-inset-right)) max(16px, env(safe-area-inset-bottom)) max(16px, env(safe-area-inset-left));
    box-sizing: border-box;
    align-items: center;
    justify-content: center;
    flex-direction: row;
}
.logistics-pickup-modal-root.is-open {
    display: flex;
}
.logistics-pickup-modal__backdrop {
    position: absolute;
    inset: 0;
    z-index: 0;
    background: rgba(15, 23, 42, 0.52);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    cursor: pointer;
}
.logistics-pickup-modal__panel {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 440px;
    max-height: min(90vh, calc(100dvh - 32px));
    overflow-x: hidden;
    overflow-y: auto;
    margin: 0;
    flex-shrink: 0;
    border: 1px solid rgba(13, 148, 136, 0.22);
    box-shadow:
        0 25px 50px -12px rgba(15, 23, 42, 0.28),
        0 0 0 1px rgba(255, 255, 255, 0.06) inset;
    -webkit-overflow-scrolling: touch;
}
.logistics-pickup-modal__hero {
    display: flex;
    gap: 16px;
    padding: 20px 22px;
    background: linear-gradient(145deg, #ecfdf5 0%, #d1fae5 50%, #ccfbf1 100%);
    border-bottom: 1px solid rgba(13, 148, 136, 0.14);
    align-items: flex-start;
}
.logistics-pickup-modal__hero-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(13, 148, 136, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.logistics-pickup-modal__hero-title {
    margin: 0 0 6px;
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--dash-ink, #0f172a);
    line-height: 1.3;
}
.logistics-pickup-modal__hero-sub {
    margin: 0;
    font-size: 0.875rem;
    line-height: 1.45;
    color: var(--dash-muted, #64748b);
}
.logistics-pickup-modal__body {
    padding: 20px 22px 22px;
    background: var(--dash-surface, #fff);
}
.logistics-pickup-modal__label {
    display: block;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--dash-muted, #64748b);
    margin-bottom: 8px;
}
.logistics-pickup-modal__textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1.5px solid var(--dash-border, #e2e8f0);
    border-radius: 10px;
    font-size: 0.9rem;
    line-height: 1.5;
    resize: vertical;
    min-height: 128px;
    font-family: inherit;
}
.logistics-pickup-modal__textarea:focus {
    outline: none;
    border-color: #0d9488;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.2);
}
.logistics-pickup-modal__error {
    margin: 10px 0 0;
    font-size: 0.8125rem;
    color: var(--dash-danger, #dc2626);
}
.logistics-pickup-modal__actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 18px;
    flex-wrap: wrap;
}
.logistics-pickup-modal__optional {
    font-weight: 500;
    color: var(--dash-muted, #64748b);
    text-transform: none;
    letter-spacing: 0;
    font-size: 0.85em;
}
.logistics-pickup-modal__field-hint {
    margin: -2px 0 10px;
    font-size: 0.8125rem;
    color: var(--dash-muted, #64748b);
    line-height: 1.45;
}
.logistics-pickup-modal__actions--split {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
}
.logistics-pickup-modal__actions-primary {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: flex-end;
}
@media (min-width: 520px) {
    .logistics-pickup-modal__actions--split {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
    .logistics-pickup-modal__actions-primary {
        flex: 1;
        justify-content: flex-end;
    }
}

@media (max-width: 900px) {
    .logistics-flow-board { grid-template-columns: 1fr !important; }
    .logistics-pickup-spotlight__inner { padding: 16px 18px; }
}
</style>
@endsection
