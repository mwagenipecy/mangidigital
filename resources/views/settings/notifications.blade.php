@extends('layouts.dashboard')

@section('title', __('Notification Preferences'))

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">{{ __('Notification Preferences') }}</h1>
        <p class="dash-page-subtitle">{{ __('Choose how you receive notifications') }}</p>
    </div>
</div>

<div class="dash-card" style="max-width: 32rem;">
    <div style="padding: 20px;">
        <p style="margin: 0; font-size: .9rem; color: var(--dash-text);">{{ __('Notification preferences will be available here.') }}</p>
    </div>
</div>
@endsection
