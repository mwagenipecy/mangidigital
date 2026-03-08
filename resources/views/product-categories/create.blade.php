@extends('layouts.dashboard')

@section('title', __('Add product category'))

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('product-categories.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            Back to categories
        </a>
        <h1 class="dash-page-title">Product category</h1>
        <p class="dash-page-subtitle">Add a category for your products</p>
    </div>
</div>

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Category details</div>
            <div class="dash-card-subtitle">Register a product category (e.g. Clothing, Electronics)</div>
        </div>
    </div>
    <form action="{{ route('product-categories.store') }}" method="POST" style="padding:0 20px 20px;">
        @csrf
        <div style="display:flex;flex-direction:column;gap:16px;max-width:400px;">
            <div>
                <label for="name" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Category name <span style="color:var(--dash-danger);">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"
                    placeholder="e.g. Clothing">
                @error('name')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="description" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Description</label>
                <textarea id="description" name="description" rows="3"
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;resize:vertical;"
                    placeholder="Optional description">{{ old('description') }}</textarea>
                @error('description')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="dash-btn dash-btn-brand" style="align-self:flex-start;">Save category</button>
        </div>
    </form>
</div>
@endsection
