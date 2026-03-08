@extends('layouts.dashboard')

@section('title', __('Add product'))

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('products.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            Back to products
        </a>
        <h1 class="dash-page-title">Product</h1>
        <p class="dash-page-subtitle">Register a product you sell (not inventory)</p>
    </div>
</div>

@if($categories->isEmpty())
    <div class="dash-card" style="background:var(--dash-brand-10);border-color:var(--dash-brand);">
        <p style="margin:0;font-size:.9rem;color:var(--dash-ink);">Add at least one <a href="{{ route('product-categories.create') }}" class="text-[var(--dash-brand)] font-semibold" wire:navigate>product category</a> before adding products.</p>
    </div>
@else
<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Product details</div>
            <div class="dash-card-subtitle">Item you sell — name, category, price</div>
        </div>
    </div>
    <form action="{{ route('products.store') }}" method="POST" style="padding:0 20px 20px;">
        @csrf
        <div style="display:flex;flex-direction:column;gap:16px;max-width:400px;">
            <div>
                <label for="name" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Product name <span style="color:var(--dash-danger);">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"
                    placeholder="e.g. T-shirt">
                @error('name')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="product_category_id" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Category <span style="color:var(--dash-danger);">*</span></label>
                <select id="product_category_id" name="product_category_id" required
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                    <option value="">Select category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('product_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('product_category_id')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="description" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Description</label>
                <textarea id="description" name="description" rows="2"
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;resize:vertical;"
                    placeholder="Optional">{{ old('description') }}</textarea>
                @error('description')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="price" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Price (TZS) <span style="color:var(--dash-danger);">*</span></label>
                <input type="number" id="price" name="price" value="{{ old('price') }}" min="0" step="1" required
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"
                    placeholder="0">
                @error('price')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="unit" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Unit</label>
                <input type="text" id="unit" name="unit" value="{{ old('unit') }}"
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"
                    placeholder="e.g. piece, kg, litre">
                @error('unit')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="dash-btn dash-btn-brand" style="align-self:flex-start;">Save product</button>
        </div>
    </form>
</div>
@endif
@endsection
