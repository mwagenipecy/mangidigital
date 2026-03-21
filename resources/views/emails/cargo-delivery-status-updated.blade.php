<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Cargo update') }}</title>
</head>
<body style="font-family:system-ui,-apple-system,sans-serif;line-height:1.5;color:#1e293b;background:#f8fafc;padding:24px;">
    <div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;padding:28px;border:1px solid #e2e8f0;">
        <p style="margin:0 0 16px;font-size:15px;">{{ __('Hello :name,', ['name' => $clientName]) }}</p>
        <p style="margin:0 0 16px;font-size:15px;">
            {{ __('Your delivery status has been updated.') }}
        </p>
        <p style="margin:0 0 8px;font-size:14px;color:#64748b;">{{ __('Reference') }}</p>
        <p style="margin:0 0 16px;font-size:16px;font-weight:600;">{{ $referenceDisplay }}</p>
        <p style="margin:0 0 8px;font-size:14px;color:#64748b;">{{ __('Current status') }}</p>
        <p style="margin:0 0 24px;font-size:18px;font-weight:700;color:#0f766e;">{{ $statusLabel }}</p>
        @if(!empty($pickupOffice))
            <div style="margin:0 0 24px;padding:16px;background:#f0fdfa;border-radius:10px;border:1px solid #99f6e4;">
                <p style="margin:0 0 8px;font-size:13px;font-weight:700;color:#0f766e;text-transform:uppercase;">{{ __('Pickup location') }}</p>
                <p style="margin:0;font-size:15px;color:#134e4a;white-space:pre-wrap;">{{ $pickupOffice }}</p>
                <p style="margin:12px 0 0;font-size:13px;color:#64748b;">{{ __('Please go to this office or address to collect your cargo.') }}</p>
            </div>
        @endif
        @if($dispatchedAtFormatted)
            <p style="margin:0 0 8px;font-size:13px;color:#64748b;">{{ __('Dispatched') }}: {{ $dispatchedAtFormatted }}</p>
        @endif
        @if($arrivedAtFormatted)
            <p style="margin:0 0 8px;font-size:13px;color:#64748b;">{{ __('Arrived') }}: {{ $arrivedAtFormatted }}</p>
        @endif
        @if($receivedAtFormatted)
            <p style="margin:0 0 24px;font-size:13px;color:#64748b;">{{ __('Received') }}: {{ $receivedAtFormatted }}</p>
        @endif
        @if($summaryLine)
            <p style="margin:0 0 8px;font-size:14px;color:#64748b;">{{ __('Details') }}</p>
            <p style="margin:0 0 24px;font-size:14px;">{{ $summaryLine }}</p>
        @endif
        <p style="margin:0 0 16px;font-size:15px;">
            <a href="{{ $trackUrl }}" style="display:inline-block;background:#0d9488;color:#fff;text-decoration:none;padding:12px 20px;border-radius:8px;font-weight:600;">{{ __('View cargo status') }}</a>
        </p>
        <p style="margin:0;font-size:12px;color:#94a3b8;">{{ $organizationName }}</p>
    </div>
</body>
</html>
