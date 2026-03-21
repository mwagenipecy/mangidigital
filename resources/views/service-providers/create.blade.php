@extends('layouts.dashboard')

@section('title', __('Add service provider'))

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('service-providers.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            Back
        </a>
        <h1 class="dash-page-title">Add clearance or transport company</h1>
        <p class="dash-page-subtitle">International transport, local transport, or clearance & forwarding</p>
    </div>
</div>

<form action="{{ route('service-providers.store') }}" method="POST">
    @csrf
    <div class="dash-card dash-form-card">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">New service provider</div>
                <div class="dash-card-subtitle">Company details and contact — type: international transport / local transport / clearance & forwarding</div>
            </div>
        </div>
        <div class="dash-form-section">
            <div class="dash-form-grid dash-form-grid--2" style="max-width:100%;">
                <div class="dash-form-field">
                    <label for="name">Company name <span style="color:var(--dash-danger);">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')<p class="dash-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="dash-form-field">
                    <label for="type">Type / identifier <span style="color:var(--dash-danger);">*</span></label>
                    <select id="type" name="type" required>
                        <option value="international_transport" {{ old('type') === 'international_transport' ? 'selected' : '' }}>International transport</option>
                        <option value="local_transport" {{ old('type') === 'local_transport' ? 'selected' : '' }}>Local transport</option>
                        <option value="clearance_forwarding" {{ old('type') === 'clearance_forwarding' ? 'selected' : '' }}>Clearance & forwarding</option>
                    </select>
                    @error('type')<p class="dash-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="dash-form-field">
                    <label for="product_category_id">Category served (optional)</label>
                    <select id="product_category_id" name="product_category_id">
                        <option value="">— All categories —</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ (string) old('product_category_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <p class="dash-form-hint" style="margin-top:6px;">When set, this transport appears when that category is selected on the sale delivery filter.</p>
                    @error('product_category_id')<p class="dash-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="dash-form-field">
                    <label for="contact_phone">Phone</label>
                    <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}">
                    @error('contact_phone')<p class="dash-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="dash-form-field">
                    <label for="contact_email">Email</label>
                    <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}">
                    @error('contact_email')<p class="dash-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="dash-form-field" style="grid-column: 1 / -1;">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}">
                    @error('address')<p class="dash-form-error">{{ $message }}</p>@enderror
                </div>
                <div class="dash-form-field" style="grid-column: 1 / -1;">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                    @error('notes')<p class="dash-form-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="dash-form-actions">
                <button type="submit" class="dash-btn dash-btn-brand">
                    <flux:icon.check class="size-4" />
                    Save
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
