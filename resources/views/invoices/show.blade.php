@extends('layouts.dashboard')

@section('title', __('Invoice') . ' ' . $invoice->display_number)

@section('content')
<div class="dash-page-header">
    <div>
        <a href="{{ route('invoices.index') }}" class="dash-btn dash-btn-outline" style="margin-bottom:8px;display:inline-flex;align-items:center;gap:6px;" wire:navigate>
            <flux:icon.arrow-left class="size-4" />
            {{ __('Back to invoices') }}
        </a>
        <h1 class="dash-page-title">{{ __('Invoice') }} {{ $invoice->display_number }}</h1>
        <p class="dash-page-subtitle">{{ $invoice->issue_date?->format('d M Y') }} — {{ $invoice->isPaid() ? __('Paid') : __('Unpaid') }}</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('invoices.pdf', $invoice) }}" class="dash-btn dash-btn-brand" target="_blank" rel="noopener">
            <flux:icon.document-arrow-down class="size-4" />
            {{ __('Download PDF') }}
        </a>
        @if($invoice->isPaid())
            <form action="{{ route('invoices.mark-unpaid', $invoice) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="dash-btn dash-btn-outline">{{ __('Mark as unpaid') }}</button>
            </form>
        @else
            <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="dash-btn dash-btn-brand">{{ __('Mark as paid') }}</button>
            </form>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="dash-card" style="margin-bottom:16px;background:var(--dash-brand-10);border-color:var(--dash-brand);">
        <p style="margin:0;font-size:.9rem;color:var(--dash-ink);">{{ session('success') }}</p>
    </div>
@endif

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">{{ __('Details') }}</div>
            <div class="dash-card-subtitle">{{ __('Origin, destination, items — source of income') }}</div>
        </div>
    </div>
    <div style="padding:0 20px 20px;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:20px;">
            <div>
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--dash-muted);margin-bottom:6px;">{{ __('Origin') }}</div>
                <div style="font-size:.9rem;white-space:pre-wrap;">{{ $invoice->origin ?? '—' }}</div>
            </div>
            <div>
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;color:var(--dash-muted);margin-bottom:6px;">{{ __('Destination') }}</div>
                <div style="font-size:.9rem;white-space:pre-wrap;">{{ $invoice->destination_name }}</div>
            </div>
        </div>
        <div style="margin-bottom:16px;">
            <span class="dash-pill {{ $invoice->isPaid() ? 'dash-pill-green' : 'dash-pill-yellow' }}">{{ $invoice->isPaid() ? __('Paid') : __('Unpaid') }}</span>
            @if($invoice->paid_at)
                <span class="dash-td-sub" style="margin-left:8px;">{{ __('Paid on') }} {{ $invoice->paid_at->format('d M Y') }}</span>
            @endif
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Qty') }}</th>
                        <th style="text-align:right;">{{ __('Unit price') }}</th>
                        <th style="text-align:right;">{{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ number_format($item->quantity, 2) }}</td>
                        <td style="text-align:right;">{{ number_format($item->unit_price, 0) }}</td>
                        <td style="text-align:right;">{{ number_format($item->amount, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:12px;text-align:right;">
            <strong style="font-size:1.1rem;">{{ __('Total (TZS)') }}: {{ number_format($invoice->total, 0) }}</strong>
        </div>
        @if($invoice->notes)
            <p style="margin-top:16px;font-size:.9rem;color:var(--dash-muted);">{{ $invoice->notes }}</p>
        @endif
        @if($invoice->issuer_name)
            <p style="margin-top:8px;font-size:.85rem;color:var(--dash-muted);">{{ __('Issuer') }}: {{ $invoice->issuer_name }}</p>
        @endif
    </div>
</div>
@endsection
