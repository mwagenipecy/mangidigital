<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    @php
        $ref = $trackType === 'sale' ? ($sale->receipt_number ?? '#' . $sale->id) : $cargo->reference_number;
        $org = $trackType === 'sale' ? $sale->organization : $cargo->organization;
        $provider = $trackType === 'sale' ? $sale->deliveryServiceProvider : $cargo->deliveryServiceProvider;
        $entity = $trackType === 'sale' ? $sale : $cargo;
    @endphp
    <title>{{ __('Cargo status') }} — {{ $ref }}</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f1f5f9; color: #0f172a; margin: 0; padding: 24px 16px; line-height: 1.5; }
        .wrap { max-width: 520px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 24px rgba(15, 23, 42, .06); }
        h1 { font-size: 1.25rem; margin: 0 0 8px; }
        .muted { color: #64748b; font-size: .875rem; }
        .status { font-size: 1.35rem; font-weight: 700; color: #0d9488; margin: 16px 0; }
        .steps { display: flex; flex-direction: column; gap: 8px; margin-top: 20px; }
        .step { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: .9rem; }
        .step.done { background: #ecfdf5; border-color: #a7f3d0; }
        .step.current { background: #f0fdfa; border-color: #5eead4; font-weight: 600; }
        .step.pending { opacity: .55; }
        .dot { width: 10px; height: 10px; border-radius: 50%; background: #cbd5e1; flex-shrink: 0; }
        .step.done .dot, .step.current .dot { background: #0d9488; }
        ul { margin: 8px 0 0; padding-left: 18px; font-size: .875rem; color: #475569; }
        .pickup { margin-top: 20px; padding: 16px; background: #f0fdfa; border-radius: 10px; border: 1px solid #99f6e4; }
        .pickup h2 { margin: 0 0 8px; font-size: .85rem; color: #0f766e; text-transform: uppercase; }
        .pickup p { margin: 0; font-size: .95rem; color: #134e4a; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="wrap">
        <p class="muted" style="margin-bottom:12px;">
            <a href="{{ route('cargo.track.form') }}" style="color:#2AA5BD;text-decoration:none;font-weight:600;">{{ __('Track another shipment') }}</a>
            <span style="color:#cbd5e1;"> · </span>
            <a href="{{ url('/') }}" style="color:#64748b;text-decoration:none;">{{ __('Home') }}</a>
        </p>
        <p class="muted">{{ $org?->name ?? config('app.name') }}</p>
        <h1>{{ __('Your delivery') }}</h1>
        <p class="muted">{{ __('Reference') }}: <strong>{{ $ref }}</strong></p>
        @if($entity->public_tracking_code)
            <p class="muted">{{ __('Tracking number') }}: <strong style="font-family:ui-monospace,monospace;letter-spacing:0.02em;">{{ $entity->formatted_public_tracking_code }}</strong></p>
        @endif
        <p class="status">{{ $entity->delivery_status_label }}</p>
        @if($provider)
            <p class="muted">{{ __('Transport') }}: {{ $provider->name }}</p>
        @endif
        @if($entity->delivery_pickup_office)
            <div class="pickup">
                <h2>{{ __('Pickup location') }}</h2>
                <p>{{ $entity->delivery_pickup_office }}</p>
                <p class="muted" style="margin-top:10px;font-size:.8rem;">{{ __('Collect your cargo at this office or address.') }}</p>
            </div>
        @endif
        <div class="steps">
            @php
                $order = [\App\Models\Sale::DELIVERY_STATUS_PENDING, \App\Models\Sale::DELIVERY_STATUS_IN_TRANSIT, \App\Models\Sale::DELIVERY_STATUS_ARRIVED, \App\Models\Sale::DELIVERY_STATUS_RECEIVED];
                $labels = [
                    \App\Models\Sale::DELIVERY_STATUS_PENDING => __('Pending'),
                    \App\Models\Sale::DELIVERY_STATUS_IN_TRANSIT => __('In transit'),
                    \App\Models\Sale::DELIVERY_STATUS_ARRIVED => __('Arrived'),
                    \App\Models\Sale::DELIVERY_STATUS_RECEIVED => __('Received by customer'),
                ];
                $cur = $entity->delivery_status ?? \App\Models\Sale::DELIVERY_STATUS_PENDING;
                $curIdx = array_search($cur, $order, true);
                if ($curIdx === false) { $curIdx = 0; }
            @endphp
            @foreach($order as $i => $st)
                @php
                    $cls = 'step pending';
                    if ($i < $curIdx) { $cls = 'step done'; }
                    elseif ($i === $curIdx) { $cls = 'step current'; }
                @endphp
                <div class="{{ $cls }}"><span class="dot"></span>{{ $labels[$st] }}</div>
            @endforeach
        </div>
        @if($trackType === 'sale' && $sale->items->isNotEmpty())
            <p class="muted" style="margin-top:20px;margin-bottom:4px;">{{ __('Items') }}</p>
            <ul>
                @foreach($sale->items as $item)
                    <li>{{ $item->display_product_name }} × {{ number_format($item->quantity, 0) }}</li>
                @endforeach
            </ul>
        @elseif($trackType === 'cargo' && $cargo->cargo_description)
            <p class="muted" style="margin-top:20px;margin-bottom:4px;">{{ __('Cargo') }}</p>
            <p style="font-size:.9rem;color:#475569;">{{ $cargo->cargo_description }}</p>
        @endif
    </div>
</body>
</html>
