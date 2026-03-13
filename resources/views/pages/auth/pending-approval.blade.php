@extends('layouts.account')

@section('title', __('Pending approval'))

@section('panel-tag')
    <div class="account-panel-tag">✦ Account status</div>
@endsection

@section('panel-title')
    <h2 class="account-panel-title">Almost<br><em>There</em></h2>
@endsection

@section('panel-desc')
    <p class="account-panel-desc">Your registration was successful. An administrator will review your account and approve access to the dashboard shortly.</p>
@endsection

@section('panel-features')
    <div class="account-feature-list">
        <div class="account-f-item"><div class="account-f-dot"><flux:icon.clock class="size-3.5 text-white" /></div>Review usually within 24 hours</div>
        <div class="account-f-item"><div class="account-f-dot"><flux:icon.envelope class="size-3.5 text-white" /></div>We may contact you if needed</div>
    </div>
@endsection

@section('content')
<div class="account-form-wrap">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('home') }}" class="account-logo account-logo-dark no-underline text-[1.1rem]" wire:navigate>
            <span class="account-logo-icon w-[30px] h-[30px] text-[.7rem]">MD</span>
            Mangi<span>Digital</span>
        </a>
    </div>

    <div class="rounded-xl border border-[var(--border-lt)] bg-white p-6 text-center" style="border-color: rgba(42,165,189,.2); background: #fff;">
        <div class="mb-4 flex justify-center">
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-[var(--brand-10)] text-[var(--brand)]">
                <flux:icon.clock class="size-8" />
            </span>
        </div>
        <h2 class="account-form-title" style="margin-bottom: 8px;">{{ __('Account pending approval') }}</h2>
        <p class="account-form-sub" style="margin-bottom: 20px;">{{ __('Your account and organization are under review. You will be able to access the dashboard once an administrator approves your registration.') }}</p>
        <p class="text-[.85rem] text-[var(--muted)] mb-6">{{ __('Thank you for your patience.') }}</p>

        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="account-btn account-btn-outline account-btn-full">
                {{ __('Sign out') }}
            </button>
        </form>
    </div>

    <p class="text-center text-[.8rem] text-[var(--muted)] mt-6">
        <a href="{{ route('home') }}" class="text-[var(--brand)]" wire:navigate>{{ __('Back to home') }}</a>
    </p>
</div>
@endsection
