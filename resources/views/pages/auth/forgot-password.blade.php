@extends('layouts.account')

@section('title', __('Forgot password'))

@section('panel-tag')
    <div class="account-panel-tag">✦ Reset password</div>
@endsection

@section('panel-title')
    <h2 class="account-panel-title">We'll Send You<br><em>a Reset Link</em></h2>
@endsection

@section('panel-desc')
    <p class="account-panel-desc">Enter the email address linked to your Mangi Digital account. We'll send you a secure link to reset your password.</p>
@endsection

@section('panel-features')
    <div class="account-feature-list">
        <div class="account-f-item"><div class="account-f-dot"><flux:icon.envelope class="size-3.5 text-white" /></div>Check your email for the link</div>
        <div class="account-f-item"><div class="account-f-dot"><flux:icon.lock-closed class="size-3.5 text-white" /></div>Link expires in 60 minutes</div>
        <div class="account-f-item"><div class="account-f-dot"><flux:icon.shield-check class="size-3.5 text-white" /></div>Secure and private</div>
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
    <h2 class="account-form-title">Forgot password</h2>
    <p class="account-form-sub">Enter your email to receive a password reset link</p>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div class="account-field">
            <label for="email">Email Address <span class="req">*</span></label>
            <div class="account-input-wrap has-icon">
                <span class="account-input-icon"><flux:icon.envelope class="size-4 text-[var(--muted)]" /></span>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@business.com" autocomplete="email" required autofocus
                    class="{{ $errors->has('email') ? 'border-red-500' : '' }}">
            </div>
            @error('email')<div class="account-field-error">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="account-btn account-btn-brand account-btn-full" data-test="email-password-reset-link-button">
            Email password reset link
        </button>
    </form>

    <p class="text-center text-[.87rem] text-[var(--muted)] mt-6">
        Or, return to
        <a href="{{ route('login') }}" class="text-[var(--brand)]" wire:navigate>log in</a>
    </p>
</div>
@endsection
