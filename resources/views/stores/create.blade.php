@extends('layouts.dashboard')

@section('title', __('Add store'))

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('stores.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            Back to stores
        </a>
        <h1 class="dash-page-title">Register store/shop</h1>
        <p class="dash-page-subtitle">Add a new store or shop for your organization</p>
    </div>
</div>

<form action="{{ route('stores.store') }}" method="POST">
    @csrf
    <div class="dash-card dash-form-card">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">Store details</div>
                <div class="dash-card-subtitle">Enter the store or shop information</div>
            </div>
        </div>
        <div class="dash-form-section">
            <div class="dash-form-grid dash-form-grid--2" style="max-width:100%;">
                <div class="dash-form-field">
                    <label for="name">Store name <span style="color:var(--dash-danger);">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="e.g. Main branch">
                    @error('name')<p class="dash-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="dash-form-field">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="Street, area">
                    @error('address')<p class="dash-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="dash-form-field">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="e.g. 0712 345 678">
                    @error('phone')<p class="dash-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="dash-form-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="store@example.com">
                    @error('email')<p class="dash-form-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="dash-form-actions">
                <button type="submit" class="dash-btn dash-btn-brand">
                    <flux:icon.check class="size-4" />
                    Save store
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
