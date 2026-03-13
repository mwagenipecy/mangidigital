@extends('layouts.dashboard')

@section('title', 'Expense categories')

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Expense categories</h1>
        <p class="dash-page-subtitle">Sections for categorizing expenses</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <button type="button" class="dash-btn dash-btn-brand" onclick="document.getElementById('addCategoryModal').classList.add('show')">Add category</button>
        <a href="{{ route('expenses.index') }}" class="dash-btn dash-btn-outline" wire:navigate>View expenses</a>
    </div>
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

<div class="dash-modal-overlay" id="addCategoryModal" role="dialog" aria-modal="true" aria-labelledby="addCategoryModalTitle" onclick="if(event.target===this) this.classList.remove('show')">
    <div class="dash-modal-dialog" onclick="event.stopPropagation()">
        <div class="dash-modal-header">
            <h2 class="dash-modal-title" id="addCategoryModalTitle">Add category</h2>
            <button type="button" class="dash-modal-close" onclick="document.getElementById('addCategoryModal').classList.remove('show')" aria-label="Close">&times;</button>
        </div>
        <div class="dash-modal-body">
            <form action="{{ route('expense-categories.store') }}" method="POST">
                @csrf
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label for="name" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Category name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required style="width:100%;max-width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;box-sizing:border-box;" placeholder="e.g. Transport, Rent">
                        @error('name')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="description" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Description</label>
                        <input type="text" id="description" name="description" value="{{ old('description') }}" style="width:100%;max-width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;box-sizing:border-box;" placeholder="Optional">
                    </div>
                    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                        <button type="button" class="dash-btn dash-btn-outline" onclick="document.getElementById('addCategoryModal').classList.remove('show')">Cancel</button>
                        <button type="submit" class="dash-btn dash-btn-brand">Add category</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
                    <td colspan="3" style="text-align:center;padding:24px;color:var(--dash-muted);">No categories yet. Click “Add category” to create one.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($errors->isNotEmpty())
<script>document.addEventListener('DOMContentLoaded', function() { document.getElementById('addCategoryModal').classList.add('show'); });</script>
@endif
@endsection
