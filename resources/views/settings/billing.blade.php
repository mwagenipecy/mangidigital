@extends('layouts.dashboard')

@section('title', __('Billing & Plan'))

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">{{ __('Billing & Plan') }}</h1>
        <p class="dash-page-subtitle">{{ __('Manage your subscription and billing') }}</p>
    </div>
</div>

<div class="dash-card" style="max-width: 32rem;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Professional Plan</div>
            <div class="dash-card-subtitle">{{ __('Your current plan') }}</div>
        </div>
    </div>
    <div style="padding: 0 20px 20px;">
        <p style="margin: 0; font-size: .9rem; color: var(--dash-text);">{{ __('Billing and plan management will be available here.') }}</p>
    </div>
</div>
@endsection
