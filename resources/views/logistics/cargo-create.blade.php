@extends('layouts.dashboard')

@section('title', __('Custom cargo'))

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('logistics.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            {{ __('Back to logistics') }}
        </a>
        <h1 class="dash-page-title">{{ __('Add custom cargo') }}</h1>
        <p class="dash-page-subtitle">{{ __('Logistics without a sale — client, optional email for tracking notifications, description, transport') }}</p>
    </div>
</div>

@if($errors->any())
    <div class="dash-card" style="margin-bottom:16px;background:rgba(239,68,68,.08);border-color:var(--dash-danger);">
        <ul style="margin:0;padding-left:18px;font-size:.9rem;color:var(--dash-danger);">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="dash-card dash-form-card" style="max-width:640px;">
    <form action="{{ route('logistics.cargo.store') }}" method="POST">
        @csrf
        <div class="dash-form-section">
            <div class="dash-form-field">
                <label for="client_name">{{ __('Client name') }} <span style="color:var(--dash-danger);">*</span></label>
                <input type="text" id="client_name" name="client_name" value="{{ old('client_name') }}" required>
            </div>
            <div class="dash-form-field">
                <label for="client_phone">{{ __('Phone') }} <span style="color:var(--dash-danger);">*</span></label>
                <input type="text" id="client_phone" name="client_phone" value="{{ old('client_phone') }}" required>
            </div>
            <div class="dash-form-field">
                <label for="client_email">{{ __('Email') }} <span style="color:var(--dash-muted);font-weight:400;">({{ __('for status emails') }})</span></label>
                <input type="email" id="client_email" name="client_email" value="{{ old('client_email') }}" placeholder="name@example.com">
            </div>
            <div class="dash-form-field">
                <label for="cargo_description">{{ __('Cargo / description') }}</label>
                <textarea id="cargo_description" name="cargo_description" rows="3" placeholder="{{ __('What is being shipped') }}">{{ old('cargo_description') }}</textarea>
            </div>
            <div class="dash-form-field">
                <label for="delivery_service_provider_id">{{ __('Transport') }}</label>
                <select id="delivery_service_provider_id" name="delivery_service_provider_id">
                    <option value="">—</option>
                    @foreach($serviceProviders as $sp)
                        <option value="{{ $sp->id }}" {{ (string) old('delivery_service_provider_id') === (string) $sp->id ? 'selected' : '' }}>{{ $sp->name }} ({{ $sp->type_label }})</option>
                    @endforeach
                </select>
            </div>
            <div class="dash-form-field">
                <label for="delivery_cost">{{ __('Delivery cost (TZS)') }}</label>
                <input type="number" id="delivery_cost" name="delivery_cost" value="{{ old('delivery_cost') }}" min="0" step="1">
            </div>
            <div class="dash-form-field">
                <label for="notes">{{ __('Internal notes') }}</label>
                <textarea id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
            </div>
            <div class="dash-form-actions">
                <button type="submit" class="dash-btn dash-btn-brand">{{ __('Create & open flow') }}</button>
            </div>
        </div>
    </form>
</div>
@endsection
