<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('New sign-in') }}</title>
</head>
<body style="font-family:system-ui,-apple-system,sans-serif;line-height:1.55;color:#1e293b;background:#f8fafc;padding:24px;">
    <div style="max-width:520px;margin:0 auto;background:#fff;border-radius:12px;padding:28px;border:1px solid #e2e8f0;">
        <h2 style="margin:0 0 12px;font-size:1.25rem;">{{ __('New sign-in') }}</h2>
        <p style="margin:0 0 16px;font-size:15px;">{{ __('Hello :name,', ['name' => $userName]) }}</p>
        <p style="margin:0 0 20px;font-size:15px;color:#475569;">
            {{ __('We noticed a successful sign-in to your :app account. If this was you, you can ignore this message.', ['app' => config('app.name')]) }}
        </p>

        <div style="margin:0 0 20px;padding:16px 18px;background:#f1f5f9;border-radius:10px;border:1px solid #e2e8f0;font-size:14px;">
            <p style="margin:0 0 12px;"><strong style="color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.04em;">{{ __('Time') }}</strong><br>{{ $loggedInAtFormatted }}</p>
            <p style="margin:0 0 12px;"><strong style="color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.04em;">{{ __('IP address') }}</strong><br><code style="background:#fff;padding:2px 8px;border-radius:4px;font-size:13px;">{{ $ipAddress }}</code></p>

            @if(!empty($ipLocation['summary']) || !empty($ipLocation['isp']))
            <p style="margin:0 0 12px;">
                <strong style="color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.04em;">{{ __('Approximate location (from IP)') }}</strong><br>
                @if(!empty($ipLocation['summary']))
                    {{ $ipLocation['summary'] }}
                @else
                    {{ __('Unavailable') }}
                @endif
                @if(!empty($ipLocation['isp']))
                    <br><span style="color:#64748b;font-size:13px;">{{ __('Network') }}: {{ $ipLocation['isp'] }}</span>
                @endif
            </p>
            @endif

            @if($browserLatitude && $browserLongitude)
            <p style="margin:0 0 12px;">
                <strong style="color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.04em;">{{ __('Browser-reported location') }}</strong><br>
                {{ __('Coordinates') }}: {{ $browserLatitude }}, {{ $browserLongitude }}
                @if($browserAccuracyMeters)
                    <br>{{ __('Accuracy') }}: ~{{ $browserAccuracyMeters }} m
                @endif
            </p>
            @endif

            <p style="margin:0;">
                <strong style="color:#64748b;font-size:12px;text-transform:uppercase;letter-spacing:.04em;">{{ __('Device / browser') }}</strong><br>
                <span style="word-break:break-all;color:#334155;font-size:13px;">{{ \Illuminate\Support\Str::limit($userAgent, 320) ?: '—' }}</span>
            </p>
        </div>

        <p style="margin:0 0 16px;font-size:14px;color:#b45309;">
            {{ __('If you did not sign in, change your password immediately and contact support.') }}
        </p>
        <p style="margin:0;font-size:13px;color:#94a3b8;">{{ config('app.name') }}</p>
    </div>
</body>
</html>
