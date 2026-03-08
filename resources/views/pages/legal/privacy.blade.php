@extends('layouts.account')

@section('title', __('Privacy Policy'))

@section('panel-tag')
    <div class="account-panel-tag">Legal</div>
@endsection

@section('panel-title')
    <h2 class="account-panel-title">Privacy<br><em>Policy</em></h2>
@endsection

@section('panel-desc')
    <p class="account-panel-desc">We take your privacy seriously. This policy explains how Mangi Digital collects, uses, and protects your personal information.</p>
@endsection

@section('panel-features')
    <div class="account-feature-list">
        <div class="account-f-item"><div class="account-f-dot">1</div>Information we collect</div>
        <div class="account-f-item"><div class="account-f-dot">2</div>How we use it</div>
        <div class="account-f-item"><div class="account-f-dot">3</div>Data security</div>
        <div class="account-f-item"><div class="account-f-dot">4</div>Your rights</div>
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

    <h2 class="account-form-title">Privacy Policy</h2>
    <p class="account-form-sub">Last updated: {{ now()->format('F j, Y') }}</p>

    <div class="prose prose-sm max-w-none text-[var(--text)] space-y-4 mt-6">
        <section>
            <h3 class="text-base font-bold text-[var(--ink)] mt-6 mb-2">1. Information we collect</h3>
            <p>We collect information you provide when you register (name, email, phone, business details), use the Service (orders, expenses, client data), and when you contact us. We may also collect technical data such as IP address and device information.</p>
        </section>

        <section>
            <h3 class="text-base font-bold text-[var(--ink)] mt-6 mb-2">2. How we use it</h3>
            <p>We use your information to provide and improve the Service, process payments, send service-related communications, and comply with legal obligations. We do not sell your personal data to third parties.</p>
        </section>

        <section>
            <h3 class="text-base font-bold text-[var(--ink)] mt-6 mb-2">3. Data security</h3>
            <p>We implement appropriate technical and organisational measures to protect your data. Payment and sensitive data are handled in line with industry standards.</p>
        </section>

        <section>
            <h3 class="text-base font-bold text-[var(--ink)] mt-6 mb-2">4. Your rights</h3>
            <p>You may access, correct, or request deletion of your personal data through your account settings or by contacting us. You may also have rights to data portability and to object to certain processing under applicable law.</p>
        </section>

        <section>
            <h3 class="text-base font-bold text-[var(--ink)] mt-6 mb-2">5. Contact</h3>
            <p>For privacy-related questions or requests, contact us using the details provided on our website or in your account.</p>
        </section>
    </div>

    <div class="mt-8 pt-6 border-t border-[var(--border)]">
        <a href="{{ route('register') }}" class="account-btn account-btn-outline inline-flex" wire:navigate>← Back to register</a>
    </div>
</div>
@endsection
