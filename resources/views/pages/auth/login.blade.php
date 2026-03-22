@extends('layouts.account')

@section('title', __('Log in'))

@section('panel-tag')
    <div class="account-panel-tag">✦ Welcome Back</div>
@endsection

@section('panel-title')
    <h2 class="account-panel-title">Your Business<br><em>Awaits You</em></h2>
@endsection

@section('panel-desc')
    <p class="account-panel-desc">Log into your Mangi Digital workspace. Manage orders, track expenses, and monitor every client payment — all in one place.</p>
@endsection

@section('panel-features')
    <div class="account-feature-list">
        <div class="account-f-item"><div class="account-f-dot"><flux:icon.shopping-cart class="size-3.5 text-white" /></div>Order & inventory management</div>
        <div class="account-f-item"><div class="account-f-dot"><flux:icon.banknotes class="size-3.5 text-white" /></div>Real-time expense tracking</div>
        <div class="account-f-item"><div class="account-f-dot"><flux:icon.users class="size-3.5 text-white" /></div>Client instalment payments</div>
        <div class="account-f-item"><div class="account-f-dot"><flux:icon.chart-bar class="size-3.5 text-white" /></div>Live sales analytics</div>
    </div>
@endsection

@section('content')
<div class="account-form-wrap">
    <div class="mb-8">
        <a href="{{ route('home') }}" class="account-logo account-logo-dark no-underline" wire:navigate>
            <span class="account-logo-icon">MD</span>
            Mangi<span>Digital</span>
        </a>
    </div>
    <h2 class="account-form-title">Welcome back</h2>
    <p class="account-form-sub">Sign in to your business account</p>

    @if (session('status'))
        <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5" id="login-form">
        @csrf
        {{-- Optional: filled by JS if the browser grants location (sign-in notification email) --}}
        <input type="hidden" name="login_geo_lat" id="login_geo_lat" value="">
        <input type="hidden" name="login_geo_lng" id="login_geo_lng" value="">
        <input type="hidden" name="login_geo_accuracy" id="login_geo_accuracy" value="">

        <div class="account-field">
            <label for="email">Email Address <span class="req">*</span></label>
            <div class="account-input-wrap has-icon">
                <span class="account-input-icon"><flux:icon.envelope class="size-4 text-[var(--muted)]" /></span>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@business.com" autocomplete="email" required
                    class="{{ $errors->has('email') ? 'border-red-500' : '' }}">
            </div>
            @error('email')<div class="account-field-error">{{ $message }}</div>@enderror
        </div>

        <div class="account-field">
            <label for="password">Password <span class="req">*</span></label>
            <div class="account-input-wrap has-icon" x-data="{ show: false }">
                <span class="account-input-icon"><flux:icon.lock-closed class="size-4 text-[var(--muted)]" /></span>
                <input :type="show ? 'text' : 'password'" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required
                    class="{{ $errors->has('password') ? 'border-red-500' : '' }}">
                <button type="button" class="account-input-eye" @click="show = !show" tabindex="-1">
                    <flux:icon.eye x-show="!show" class="size-4" />
                    <flux:icon.eye-slash x-show="show" class="size-4" x-cloak />
                </button>
            </div>
            @error('password')<div class="account-field-error">{{ $message }}</div>@enderror
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="account-check-row mb-0">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" class="text-[.83rem]">Keep me signed in</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-[.83rem] text-[var(--brand)]" wire:navigate>Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="account-btn account-btn-brand account-btn-full">Sign In →</button>
    </form>

    <div class="account-or-divider">or continue with</div>
    <div class="flex gap-2">
        <button type="button" class="account-btn account-btn-outline account-btn-full text-[.85rem]" disabled>Google (coming soon)</button>
        <button type="button" class="account-btn account-btn-outline account-btn-full text-[.85rem]" disabled>M-Pesa SSO (coming soon)</button>
    </div>

    <p class="text-center text-[.87rem] text-[var(--muted)] mt-6">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-[var(--brand)]" wire:navigate>Create one free →</a>
    </p>
</div>

@push('account-scripts')
<script>
(function () {
    if (!navigator.geolocation) return;
    var latEl = document.getElementById('login_geo_lat');
    var lngEl = document.getElementById('login_geo_lng');
    var accEl = document.getElementById('login_geo_accuracy');
    if (!latEl || !lngEl || !accEl) return;
    navigator.geolocation.getCurrentPosition(
        function (pos) {
            latEl.value = pos.coords.latitude.toFixed(6);
            lngEl.value = pos.coords.longitude.toFixed(6);
            if (typeof pos.coords.accuracy === 'number' && isFinite(pos.coords.accuracy)) {
                accEl.value = String(Math.round(pos.coords.accuracy));
            }
        },
        function () { /* user denied or timeout — IP-only location in email */ },
        { enableHighAccuracy: false, timeout: 10000, maximumAge: 300000 }
    );
})();
</script>
@endpush
@endsection

