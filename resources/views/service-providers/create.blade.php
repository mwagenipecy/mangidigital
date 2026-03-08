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

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">New service provider</div>
            <div class="dash-card-subtitle">Identifier: international transport / local transport / clearance & forwarding</div>
        </div>
    </div>
    <form action="{{ route('service-providers.store') }}" method="POST" style="padding:0 20px 20px;">
        @csrf
        <div style="display:flex;flex-direction:column;gap:16px;max-width:420px;">
            <div>
                <label for="name" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Company name <span style="color:var(--dash-danger);">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                @error('name')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="type" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Type / identifier <span style="color:var(--dash-danger);">*</span></label>
                <select id="type" name="type" required
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                    <option value="international_transport" {{ old('type') === 'international_transport' ? 'selected' : '' }}>International transport</option>
                    <option value="local_transport" {{ old('type') === 'local_transport' ? 'selected' : '' }}>Local transport</option>
                    <option value="clearance_forwarding" {{ old('type') === 'clearance_forwarding' ? 'selected' : '' }}>Clearance & forwarding</option>
                </select>
                @error('type')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="contact_phone" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Phone</label>
                <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}"
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                @error('contact_phone')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="contact_email" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Email</label>
                <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}"
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                @error('contact_email')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="address" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Address</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}"
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                @error('address')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="notes" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Notes</label>
                <textarea id="notes" name="notes" rows="2" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">{{ old('notes') }}</textarea>
                @error('notes')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="dash-btn dash-btn-brand" style="align-self:flex-start;">Save</button>
        </div>
    </form>
</div>
@endsection
