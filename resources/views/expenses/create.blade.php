@extends('layouts.dashboard')

@section('title', 'Add expense')

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('expenses.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            Back to expenses
        </a>
        <h1 class="dash-page-title">Add expense</h1>
        <p class="dash-page-subtitle">Category, reason, amount — upload receipt if you have it</p>
    </div>
</div>

@if($errors->any())
    <div class="dash-card" style="margin-bottom:16px;background:rgba(239,68,68,.08);border-color:var(--dash-danger);">
        <ul style="margin:0;padding-left:18px;font-size:.9rem;color:var(--dash-danger);">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">New expense</div>
            <div class="dash-card-subtitle">Categorize by section and add reason</div>
        </div>
    </div>
    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" style="padding:0 20px 20px;">
        @csrf
        <div style="display:flex;flex-direction:column;gap:16px;max-width:480px;">
            <div>
                <label for="expense_category_id" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Category (section) *</label>
                <select id="expense_category_id" name="expense_category_id" required style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                    <option value="">Select category</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('expense_category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('expense_category_id')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="amount" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Amount (TZS) *</label>
                <input type="number" id="amount" name="amount" value="{{ old('amount') }}" min="0" step="1" required style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                @error('amount')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="expense_date" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Date *</label>
                <input type="date" id="expense_date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                @error('expense_date')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="reason" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Reason *</label>
                <textarea id="reason" name="reason" rows="3" required style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">{{ old('reason') }}</textarea>
                @error('reason')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="receipt" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">Upload receipt (optional)</label>
                <input type="file" id="receipt" name="receipt" accept=".jpg,.jpeg,.png,.pdf" style="width:100%;padding:8px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                <p style="margin:6px 0 0;font-size:.8rem;color:var(--dash-muted);">JPEG, PNG or PDF, max 10 MB</p>
                @error('receipt')<p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="dash-btn dash-btn-brand" style="align-self:flex-start;">Save expense</button>
        </div>
    </form>
</div>
@endsection
