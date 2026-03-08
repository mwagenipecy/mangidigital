@extends('layouts.account')

@section('title', __('Terms of Service'))

@section('panel-tag')
    <div class="account-panel-tag">Legal</div>
@endsection

@section('panel-title')
    <h2 class="account-panel-title">Terms of<br><em>Service</em></h2>
@endsection

@section('panel-desc')
    <p class="account-panel-desc">Please read these terms carefully before using Mangi Digital. By registering or using our services, you agree to be bound by these terms.</p>
@endsection

@section('panel-features')
    <div class="account-feature-list">
        <div class="account-f-item"><div class="account-f-dot">1</div>Acceptance of terms</div>
        <div class="account-f-item"><div class="account-f-dot">2</div>Use of the service</div>
        <div class="account-f-item"><div class="account-f-dot">3</div>Subscription & payment</div>
        <div class="account-f-item"><div class="account-f-dot">4</div>Data & privacy</div>
    </div>
@endsection

@section('content')
<div class="account-form-wrap">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('home') }}" class="account-logo account-logo-dark no-underline text-[1.1rem]" wire:navigate>
            <span class="account-logo-icon w-[30px] h-[30px] text-[.7rem]">MD</span>
            Mangi<span>Digital</span>
        </a>
        <a href="{{ route('register') }}" class="text-[.82rem] text-[var(--muted)]" wire:navigate>← Back to register</a>
    </div>

    <h2 class="account-form-title">Terms of Service</h2>
    <p class="account-form-sub">Last updated: {{ now()->format('F j, Y') }}</p>

    <div class="prose prose-sm max-w-none text-[var(--text)] space-y-4 mt-6">
        <section>
            <h3 class="text-base font-bold text-[var(--ink)] mt-6 mb-2">1. Acceptance of terms</h3>
            <p>By accessing or using Mangi Digital (“the Service”), you agree to be bound by these Terms of Service and our Privacy Policy. If you do not agree, do not use the Service.</p>
        </section>

        <section>
            <h3 class="text-base font-bold text-[var(--ink)] mt-6 mb-2">2. Use of the service</h3>
            <p>Mangi Digital provides business management tools including order management, expense tracking, client and payment tracking, and related features. You must use the Service only for lawful purposes and in accordance with these terms. You are responsible for maintaining the confidentiality of your account and for all activity under your account.</p>
        </section>

        <section>
            <h3 class="text-base font-bold text-[var(--ink)] mt-6 mb-2">3. Subscription and payment</h3>
            <p>Subscription plans (Basic, Professional, Business+) are billed according to the selected billing cycle (monthly or annual). Fees are in Tanzanian Shillings (TZS). You may cancel or change your plan in accordance with the options provided in your account. Refunds are subject to our refund policy as stated at the time of purchase.</p>
        </section>

        <section>
            <h3 class="text-base font-bold text-[var(--ink)] mt-6 mb-2">4. Data and privacy</h3>
            <p>Your use of the Service is also governed by our <a href="{{ route('privacy') }}" class="text-[var(--brand)]" wire:navigate>Privacy Policy</a>. We process your data in accordance with applicable law and our Privacy Policy.</p>
        </section>

        <section>
            <h3 class="text-base font-bold text-[var(--ink)] mt-6 mb-2">5. Limitation of liability</h3>
            <p>To the fullest extent permitted by law, Mangi Digital shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of the Service.</p>
        </section>

        <section>
            <h3 class="text-base font-bold text-[var(--ink)] mt-6 mb-2">6. Contact</h3>
            <p>For questions about these Terms of Service, contact us at the details provided on our website or in your account.</p>
        </section>
    </div>

    <div class="mt-8 pt-6 border-t border-[var(--border)]">
        <a href="{{ route('register') }}" class="account-btn account-btn-outline inline-flex" wire:navigate>← Back to register</a>
    </div>
</div>
@endsection
