<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Account') — {{ config('app.name') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/css/account.css', 'resources/js/app.js'])
    @fluxAppearance
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    @stack('account-styles')
</head>
<body class="min-h-screen bg-[#f4fafc] text-[#1e3a44] font-['DM Sans'] antialiased">
    <div class="account-split">
        {{-- Left panel (branding) --}}
        <div class="account-split-left">
            <a href="{{ route('home') }}" class="account-logo" wire:navigate>
                <span class="account-logo-icon">MD</span>
                Mangi<span>Digital</span>
            </a>
            <div class="account-panel-body">
                @yield('panel-tag')
                @yield('panel-title')
                @yield('panel-desc')
                @yield('panel-features')
            </div>
            <div class="account-panel-footer">© {{ date('Y') }} Mangi Digital Ltd. Made in Tanzania</div>
        </div>
        {{-- Right panel (form) --}}
        <div class="account-split-right">
            @yield('content')
        </div>
    </div>
    @fluxScripts
    @stack('account-scripts')
</body>
</html>
