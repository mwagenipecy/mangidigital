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

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Store details</div>
            <div class="dash-card-subtitle">Enter the store or shop information</div>
        </div>
    </div>
    <form action="{{ route('stores.store') }}" method="POST" style="padding:0 20px 20px;">
        @csrf
        <div style="display:flex;flex-direction:column;gap:16px;max-width:400px;">
            <div>
                <label for="name" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Store name <span style="color:var(--dash-danger);">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"
                    placeholder="e.g. Main branch">
                @error('name')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="address" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Address</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}"
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"
                    placeholder="Street, area">
                @error('address')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Phone</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"
                    placeholder="e.g. 0712 345 678">
                @error('phone')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"
                    placeholder="store@example.com">
                @error('email')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="dash-btn dash-btn-brand" style="align-self:flex-start;">Save store</button>
        </div>
    </form>
</div>
@endsection
