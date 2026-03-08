@extends('layouts.dashboard')

@section('title', __('Generate invoice'))

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('invoices.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            {{ __('Back to invoices') }}
        </a>
        <h1 class="dash-page-title">{{ __('Generate invoice') }}</h1>
        <p class="dash-page-subtitle">{{ __('Origin, destination, items — PDF with signature place') }}</p>
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
            <div class="dash-card-title">{{ __('New invoice') }}</div>
            <div class="dash-card-subtitle">{{ __('Client optional — fill origin and destination for PDF') }}</div>
        </div>
    </div>
    <form action="{{ route('invoices.store') }}" method="POST" style="padding:0 20px 20px;">
        @csrf
        <div style="display:grid;gap:20px;max-width:640px;">
            <div>
                <label for="client_id" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">{{ __('Client (optional)') }}</label>
                <select id="client_id" name="client_id" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                    <option value="">— {{ __('No client') }} —</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} @if($c->phone)({{ $c->phone }})@endif</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="origin" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">{{ __('Origin (issuer address / from)') }}</label>
                <textarea id="origin" name="origin" rows="3" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">{{ old('origin', $organization->name) }}</textarea>
            </div>
            <div>
                <label for="destination" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">{{ __('Destination (client / bill to)') }}</label>
                <textarea id="destination" name="destination" rows="3" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">{{ old('destination') }}</textarea>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label for="issue_date" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">{{ __('Issue date') }} *</label>
                    <input type="date" id="issue_date" name="issue_date" value="{{ old('issue_date', now()->format('Y-m-d')) }}" required style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                </div>
                <div>
                    <label for="due_date" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">{{ __('Due date') }}</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                </div>
            </div>
            <div>
                <label for="issuer_name" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">{{ __('Issuer name (on PDF)') }}</label>
                <input type="text" id="issuer_name" name="issuer_name" value="{{ old('issuer_name') ?? auth()->user()->name }}" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
            <div>
                <label style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:8px;">{{ __('Items') }} *</label>
                <div id="invoice-items">
                    @php $invoiceItems = old('items') ?? [['description' => '', 'quantity' => 1, 'unit_price' => '']]; @endphp
                    @foreach($invoiceItems as $i => $item)
                    <div class="dash-invoice-row" style="display:grid;grid-template-columns:2fr 80px 120px 40px;gap:8px;align-items:end;margin-bottom:10px;">
                        <div>
                            <input type="text" name="items[{{ $i }}][description]" value="{{ $item['description'] ?? '' }}" placeholder="{{ __('Description') }}" required style="width:100%;padding:8px 12px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                        </div>
                        <div>
                            <input type="number" name="items[{{ $i }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" min="0.01" step="0.01" placeholder="Qty" required style="width:100%;padding:8px 12px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                        </div>
                        <div>
                            <input type="number" name="items[{{ $i }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}" min="0" step="1" placeholder="{{ __('Unit price') }}" required style="width:100%;padding:8px 12px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                        </div>
                        <div>
                            @if($i > 0)
                                <button type="button" class="dash-btn dash-btn-outline" style="padding:6px 10px;font-size:.8rem;" onclick="this.closest('.dash-invoice-row').remove()">×</button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="dash-btn dash-btn-outline" style="margin-top:8px;font-size:.85rem;" id="add-invoice-item">+ {{ __('Add line') }}</button>
            </div>
            <div>
                <label for="notes" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">{{ __('Notes') }}</label>
                <textarea id="notes" name="notes" rows="2" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">{{ old('notes') }}</textarea>
            </div>
            <div>
                <button type="submit" class="dash-btn dash-btn-brand">{{ __('Create invoice') }}</button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function() {
  var container = document.getElementById('invoice-items');
  var addBtn = document.getElementById('add-invoice-item');
  if (!container || !addBtn) return;
  var index = container.querySelectorAll('.dash-invoice-row').length;
  addBtn.addEventListener('click', function() {
    var row = document.createElement('div');
    row.className = 'dash-invoice-row';
    row.style.cssText = 'display:grid;grid-template-columns:2fr 80px 120px 40px;gap:8px;align-items:end;margin-bottom:10px;';
    row.innerHTML = '<div><input type="text" name="items[' + index + '][description]" placeholder="{{ __("Description") }}" required style="width:100%;padding:8px 12px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"></div>' +
      '<div><input type="number" name="items[' + index + '][quantity]" value="1" min="0.01" step="0.01" placeholder="Qty" required style="width:100%;padding:8px 12px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"></div>' +
      '<div><input type="number" name="items[' + index + '][unit_price]" min="0" step="1" placeholder="{{ __("Unit price") }}" required style="width:100%;padding:8px 12px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;"></div>' +
      '<div><button type="button" class="dash-btn dash-btn-outline" style="padding:6px 10px;font-size:.8rem;" onclick="this.closest(\'.dash-invoice-row\').remove()">×</button></div>';
    container.appendChild(row);
    index++;
  });
})();
</script>
@endpush
@endsection
