@extends('layouts.dashboard')

@section('title', __('Business Settings'))

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">{{ __('Business Settings') }}</h1>
        <p class="dash-page-subtitle">{{ __('Your organization and business details') }}</p>
    </div>
</div>

<div class="dash-card" style="max-width: 32rem;">
    @if(auth()->user()->organization)
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ auth()->user()->organization->name }}</div>
                <div class="dash-card-subtitle">{{ __('Organization') }}</div>
            </div>
        </div>
        <div style="padding: 0 20px 20px;">
            <p style="margin: 0 0 12px; font-size: .9rem; color: var(--dash-text);">
                <strong>{{ __('Status') }}:</strong> {{ auth()->user()->organization->status ?? '—' }}
            </p>
            @if(auth()->user()->organization->subscription_start)
                <p style="margin: 0 0 12px; font-size: .9rem; color: var(--dash-text);">
                    <strong>{{ __('Subscription') }}:</strong>
                    {{ auth()->user()->organization->subscription_start->format('d M Y') }}
                    @if(auth()->user()->organization->subscription_end)
                        – {{ auth()->user()->organization->subscription_end->format('d M Y') }}
                    @endif
                </p>
            @endif
        </div>
    @else
        <p style="margin: 0; padding: 20px; font-size: .9rem; color: var(--dash-muted);">{{ __('No organization linked to your account.') }}</p>
    @endif
</div>
@endsection
