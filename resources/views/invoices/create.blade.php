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

<form action="{{ route('invoices.store') }}" method="POST">
    @csrf

    <div class="dash-card dash-form-card">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Client & details') }}</div>
                <div class="dash-card-subtitle">{{ __('Optional client — origin and destination appear on the PDF') }}</div>
            </div>
        </div>
        <div class="dash-form-section">
            <div class="dash-form-grid dash-form-grid--2" style="max-width:100%;">
                <div class="dash-form-field">
                    <label for="client_id">{{ __('Client (optional)') }}</label>
                    <select id="client_id" name="client_id">
                        <option value="">— {{ __('No client') }} —</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} @if($c->phone)({{ $c->phone }})@endif</option>
                        @endforeach
                    </select>
                </div>
                <div class="dash-form-field">
                    <label for="issuer_name">{{ __('Issuer name (on PDF)') }}</label>
                    <input type="text" id="issuer_name" name="issuer_name" value="{{ old('issuer_name') ?? auth()->user()->name }}">
                </div>
            </div>
        </div>
    </div>

    <div class="dash-card dash-form-card">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Origin & destination') }}</div>
                <div class="dash-card-subtitle">{{ __('From and bill-to addresses for the PDF') }}</div>
            </div>
        </div>
        <div class="dash-form-section">
            <div class="dash-form-grid dash-form-grid--2" style="max-width:100%;">
                <div class="dash-form-field">
                    <label for="origin">{{ __('Origin (issuer address / from)') }}</label>
                    <textarea id="origin" name="origin" rows="3">{{ old('origin', $organization->name) }}</textarea>
                </div>
                <div class="dash-form-field">
                    <label for="destination">{{ __('Destination (client / bill to)') }}</label>
                    <textarea id="destination" name="destination" rows="3">{{ old('destination') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="dash-card dash-form-card">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Dates') }}</div>
                <div class="dash-card-subtitle">{{ __('Issue and due date for the invoice') }}</div>
            </div>
        </div>
        <div class="dash-form-section">
            <div class="dash-form-grid dash-form-grid--2">
                <div class="dash-form-field">
                    <label for="issue_date">{{ __('Issue date') }} <span style="color:var(--dash-danger);">*</span></label>
                    <input type="date" id="issue_date" name="issue_date" value="{{ old('issue_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="dash-form-field">
                    <label for="due_date">{{ __('Due date') }}</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="dash-card dash-form-card">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Items') }}</div>
                <div class="dash-card-subtitle">{{ __('Description, quantity and unit price per line') }}</div>
            </div>
        </div>
        <div class="dash-form-section">
            <div class="dash-form-field" style="max-width:100%;">
                <label>{{ __('Items') }} <span style="color:var(--dash-danger);">*</span></label>
                <div id="invoice-items" class="dash-invoice-items">
                    @php $invoiceItems = old('items') ?? [['description' => '', 'quantity' => 1, 'unit_price' => '']]; @endphp
                    @foreach($invoiceItems as $i => $item)
                    <div class="dash-invoice-row">
                        <div>
                            <input type="text" name="items[{{ $i }}][description]" value="{{ $item['description'] ?? '' }}" placeholder="{{ __('Description') }}" required>
                        </div>
                        <div>
                            <input type="number" name="items[{{ $i }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" min="0.01" step="0.01" placeholder="Qty" required>
                        </div>
                        <div>
                            <input type="number" name="items[{{ $i }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}" min="0" step="1" placeholder="{{ __('Unit price') }}" required>
                        </div>
                        <div>
                            @if($i > 0)
                                <button type="button" class="dash-btn dash-btn-outline dash-invoice-remove" style="padding:6px 10px;font-size:.8rem;" aria-label="{{ __('Remove line') }}">×</button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="dash-btn dash-btn-outline dash-invoice-add" id="add-invoice-item">+ {{ __('Add line') }}</button>
            </div>
        </div>
    </div>

    <div class="dash-card dash-form-card">
        <div class="dash-form-section">
            <div class="dash-form-field" style="max-width:100%;">
                <label for="notes">{{ __('Notes') }}</label>
                <textarea id="notes" name="notes" rows="2" placeholder="{{ __('Optional notes on the invoice') }}">{{ old('notes') }}</textarea>
            </div>
            <div class="dash-form-actions">
                <button type="submit" class="dash-btn dash-btn-brand">
                    <flux:icon.check class="size-4" />
                    {{ __('Create invoice') }}
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
(function() {
  var container = document.getElementById('invoice-items');
  var addBtn = document.getElementById('add-invoice-item');
  if (!container || !addBtn) return;
  var descPlaceholder = {{ json_encode(__('Description')) }};
  var pricePlaceholder = {{ json_encode(__('Unit price')) }};
  addBtn.addEventListener('click', function() {
    var index = container.querySelectorAll('.dash-invoice-row').length;
    var row = document.createElement('div');
    row.className = 'dash-invoice-row';
    row.innerHTML = '<div><input type="text" name="items[' + index + '][description]" placeholder="' + descPlaceholder + '" required></div>' +
      '<div><input type="number" name="items[' + index + '][quantity]" value="1" min="0.01" step="0.01" placeholder="Qty" required></div>' +
      '<div><input type="number" name="items[' + index + '][unit_price]" min="0" step="1" placeholder="' + pricePlaceholder + '" required></div>' +
      '<div><button type="button" class="dash-btn dash-btn-outline dash-invoice-remove" style="padding:6px 10px;font-size:.8rem;" aria-label="{{ __("Remove line") }}">×</button></div>';
    container.appendChild(row);
  });
  container.addEventListener('click', function(e) {
    if (e.target.classList.contains('dash-invoice-remove')) e.target.closest('.dash-invoice-row').remove();
  });
})();
</script>
@endpush
@endsection
