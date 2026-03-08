@extends('layouts.dashboard')

@section('title', __('Invoices'))

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">{{ __('Invoices') }}</h1>
        <p class="dash-page-subtitle">{{ __('Generate and manage invoices — filter by month') }}</p>
    </div>
    <a href="{{ route('invoices.create') }}" class="dash-btn dash-btn-brand" wire:navigate>
        <flux:icon.plus class="size-4" />
        {{ __('Generate invoice') }}
    </a>
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

<form method="GET" action="{{ route('invoices.index') }}" style="margin-bottom:20px;">
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <label for="month" style="font-size:.85rem;font-weight:600;color:var(--dash-ink);">{{ __('Month') }}</label>
        <select id="month" name="month" style="padding:8px 12px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            <option value="" {{ $month === '' ? 'selected' : '' }}>{{ __('All months') }}</option>
            @foreach($monthsAvailable as $m)
                <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($m . '-01')->format('F Y') }}</option>
            @endforeach
        </select>
        <button type="submit" class="dash-btn dash-btn-outline" style="font-size:.85rem;">{{ __('Filter') }}</button>
    </div>
</form>

<div class="dash-kpi-grid dash-kpi-grid--three" style="margin-bottom:24px;">
    <x-dashboard.kpi-card :value="(string)$monthCount" :label="__('Invoices this month')" color="var(--dash-brand)" bg="var(--dash-brand-10)">
        <x-slot:icon><flux:icon.document-text class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="number_format($monthTotal, 0)" :label="__('Total this month (TZS)')" color="var(--dash-ok)" bg="rgba(34,197,94,.08)">
        <x-slot:icon><flux:icon.banknotes class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="(string)$paidCount" :label="__('Paid')" color="var(--dash-ok)" bg="rgba(34,197,94,.08)">
        <x-slot:icon><flux:icon.check-circle class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
</div>
<div class="dash-kpi-grid" style="grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:24px;">
    <x-dashboard.kpi-card :value="(string)$unpaidCount" label="{{ __('Unpaid / draft') }}" color="var(--dash-warn)" bg="rgba(245,158,11,.08)">
        <x-slot:icon><flux:icon.clock class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="(string)$totalInvoices" label="{{ __('All time invoices') }}" color="var(--dash-purple)" bg="rgba(139,92,246,.1)">
        <x-slot:icon><flux:icon.document-text class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
</div>

<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">{{ $month ? $monthCarbon->format('F Y') . ' — ' : '' }}{{ __('Invoices') }}</div>
            <div class="dash-card-subtitle">{{ __('Source of income: mark as paid when received') }}</div>
        </div>
    </div>
    <div class="dash-table-wrap">
        <table class="dash-table">
            <thead>
                <tr>
                    <th>{{ __('Number') }}</th>
                    <th>{{ __('Client / Destination') }}</th>
                    <th>{{ __('Issue date') }}</th>
                    <th>{{ __('Total (TZS)') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td><span style="font-family:'Space Mono',monospace;font-size:.85rem;color:var(--dash-brand);">{{ $inv->display_number }}</span></td>
                    <td>
                        @if($inv->client)
                            <span class="dash-td-main">{{ $inv->client->name }}</span>
                            @if($inv->client->phone)<div class="dash-td-sub">{{ $inv->client->phone }}</div>@endif
                        @else
                            <span class="dash-td-sub" style="max-width:200px;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ Str::limit($inv->destination ?? '—', 40) }}</span>
                        @endif
                    </td>
                    <td><span class="dash-td-sub">{{ $inv->issue_date?->format('d M Y') }}</span></td>
                    <td><span class="dash-td-amount">{{ number_format($inv->total, 0) }}</span></td>
                    <td>
                        @if($inv->status === 'paid')
                            <span class="dash-pill dash-pill-green">{{ __('Paid') }}</span>
                        @elseif($inv->status === 'sent')
                            <span class="dash-pill dash-pill-blue">{{ __('Sent') }}</span>
                        @else
                            <span class="dash-pill dash-pill-yellow">{{ __('Draft') }}</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;flex-wrap:wrap;gap:6px;">
                            <a href="{{ route('invoices.show', $inv) }}" class="dash-btn dash-btn-outline" style="padding:5px 10px;font-size:.75rem;" wire:navigate>{{ __('View') }}</a>
                            <a href="{{ route('invoices.pdf', $inv) }}" class="dash-btn dash-btn-outline" style="padding:5px 10px;font-size:.75rem;" target="_blank" rel="noopener">{{ __('PDF') }}</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:24px;color:var(--dash-muted);">
                        {{ __('No invoices for this month.') }}
                        <a href="{{ route('invoices.create') }}" style="color:var(--dash-brand);" wire:navigate>{{ __('Generate invoice') }}</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
        <div style="padding:12px 16px;border-top:1px solid var(--dash-border);display:flex;justify-content:center;gap:8px;">
            @if($invoices->previousPageUrl())
                <a href="{{ $invoices->previousPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>← {{ __('Previous') }}</a>
            @endif
            <span style="align-self:center;font-size:.85rem;color:var(--dash-muted);">{{ __('Page') }} {{ $invoices->currentPage() }} {{ __('of') }} {{ $invoices->lastPage() }}</span>
            @if($invoices->nextPageUrl())
                <a href="{{ $invoices->nextPageUrl() }}" class="dash-btn dash-btn-outline" wire:navigate>{{ __('Next') }} →</a>
            @endif
        </div>
    @endif
</div>
@endsection
