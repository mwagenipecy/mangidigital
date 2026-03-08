@extends('layouts.dashboard')

@section('title', 'Expense categories')

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Expense categories</h1>
        <p class="dash-page-subtitle">Sections for categorizing expenses</p>
    </div>
    <a href="{{ route('expenses.index') }}" class="dash-btn dash-btn-outline" wire:navigate>View expenses</a>
</div>

@if(session('error'))
    <div class="dash-card" style="margin-bottom:16px;background:rgba(239,68,68,.08);border-color:var(--dash-danger);">
        <p style="margin:0;font-size:.9rem;color:var(--dash-danger);">{{ session('error') }}</p>
    </div>
@endif
@if(session('success'))
    <div class="dash-card" style="margin-bottom:16px;background:var(--dash-brand-10);border-color:var(--dash-brand);">
        <p style="margin:0;font-size:.9rem;color:var(--dash-ink);">{{ session('success') }}</p>
    </div>
@endif

<div class="dash-card" style="margin-bottom:20px;">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Add category</div>
            <div class="dash-card-subtitle">Name and optional description</div>
        </div>
    </div>
    <form action="{{ route('expense-categories.store') }}" method="POST" style="padding:0 20px 20px;">
        @csrf
        <div style="display:flex;flex-wrap:wrap;gap:16px;align-items:flex-end;">
            <div>
                <label for="name" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Category name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required style="width:220px;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;" placeholder="e.g. Transport, Rent">
                @error('name')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="description" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Description</label>
                <input type="text" id="description" name="description" value="{{ old('description') }}" style="width:260px;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;" placeholder="Optional">
            </div>
            <button type="submit" class="dash-btn dash-btn-brand">Add category</button>
        </div>
    </form>
</div>

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">Categories</div>
            <div class="dash-card-subtitle">Used when adding expenses</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td><span class="dash-td-main">{{ $cat->name }}</span></td>
                    <td><span class="dash-td-sub">{{ $cat->description ?? '—' }}</span></td>
                    <td>
                        @if($cat->expenses()->exists())
                            <span class="dash-td-sub" style="font-size:.85rem;">In use</span>
                        @else
                            <form action="{{ route('expense-categories.destroy', $cat) }}" method="POST" style="display:inline;" onsubmit="return confirm('Remove this category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dash-btn" style="padding:5px 10px;font-size:.75rem;background:var(--dash-danger);color:white;border:none;cursor:pointer;">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center;padding:24px;color:var(--dash-muted);">No categories yet. Add one above.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
