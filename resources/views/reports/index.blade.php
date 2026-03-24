@extends('layouts.dashboard')

@section('title', __('Business reports'))

@php
    $r = $report;
    $fmt = fn ($v) => number_format((float) $v, 0);
    $activeTab = request('tab', 'overview');
    $activeRange = request('range', 'this_month');
@endphp

@section('content')
<style>
.rpt-tabs{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:24px;padding:10px;border:1px solid var(--dash-border);border-radius:12px;background:#f8fafc;}
.rpt-tab{display:inline-flex;align-items:center;justify-content:center;padding:10px 16px;font-size:.84rem;font-weight:700;color:var(--dash-muted);text-decoration:none;cursor:pointer;white-space:nowrap;border:1px solid transparent;border-radius:10px;transition:all .15s ease;user-select:none;}
.rpt-tab:hover{color:var(--dash-ink);background:#eef2f7;border-color:#dbe3ee;}
.rpt-tab.is-active{color:#fff;background:var(--dash-brand);border-color:var(--dash-brand);box-shadow:0 6px 16px rgba(37,99,235,.2);}
.rpt-panel{display:none;animation:rptFade .2s ease;}
.rpt-panel.is-active{display:block;}
@keyframes rptFade{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}
</style>

<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">{{ __('Reports') }}</h1>
        <p class="dash-page-subtitle">{{ __('Revenue, expenses, cost of goods, profit & loss — :from to :to', ['from' => $range['from_display'], 'to' => $range['to_display']]) }}</p>
    </div>
</div>

{{-- Date range filter --}}
<form method="GET" action="{{ route('reports.index') }}" class="dash-card" style="margin-bottom:20px;padding:16px 20px;" id="reportFilterForm">
    <input type="hidden" name="tab" value="{{ $activeTab }}">
    <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
        <div>
            <label for="range" style="display:block;font-size:.75rem;font-weight:700;color:var(--dash-muted);margin-bottom:4px;text-transform:uppercase;">{{ __('Period') }}</label>
            <select name="range" id="range" onchange="if(this.value==='custom'){document.getElementById('customDates').style.display='flex';}else{document.getElementById('customDates').style.display='none';this.form.submit();}" style="padding:9px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                @foreach([
                    'today' => __('Today'),
                    'this_week' => __('This week'),
                    'this_month' => __('This month'),
                    'last_month' => __('Last month'),
                    'this_year' => __('This year'),
                    'all_time' => __('All time'),
                    'custom' => __('Custom range'),
                ] as $key => $label)
                    <option value="{{ $key }}" {{ $range['preset'] === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div id="customDates" style="{{ $range['preset'] === 'custom' ? 'display:flex;' : 'display:none;' }}gap:10px;align-items:flex-end;">
            <div>
                <label for="from" style="display:block;font-size:.75rem;font-weight:700;color:var(--dash-muted);margin-bottom:4px;">{{ __('From') }}</label>
                <input type="date" name="from" id="from" value="{{ $range['from']->format('Y-m-d') }}" style="padding:9px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
            <div>
                <label for="to" style="display:block;font-size:.75rem;font-weight:700;color:var(--dash-muted);margin-bottom:4px;">{{ __('To') }}</label>
                <input type="date" name="to" id="to" value="{{ $range['to']->format('Y-m-d') }}" style="padding:9px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            </div>
            <button type="submit" class="dash-btn dash-btn-brand">{{ __('Apply') }}</button>
        </div>
    </div>
</form>

{{-- Tabs --}}
<div class="rpt-tabs" role="tablist">
    <a class="rpt-tab {{ $activeTab === 'overview' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $activeTab === 'overview' ? 'true' : 'false' }}" href="{{ route('reports.index', array_merge(request()->query(), ['tab' => 'overview', 'range' => $activeRange])) }}" wire:navigate>{{ __('Overview') }}</a>
    <a class="rpt-tab {{ $activeTab === 'sales' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $activeTab === 'sales' ? 'true' : 'false' }}" href="{{ route('reports.index', array_merge(request()->query(), ['tab' => 'sales', 'range' => $activeRange])) }}" wire:navigate>{{ __('Sales') }}</a>
    <a class="rpt-tab {{ $activeTab === 'logistics' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $activeTab === 'logistics' ? 'true' : 'false' }}" href="{{ route('reports.index', array_merge(request()->query(), ['tab' => 'logistics', 'range' => $activeRange])) }}" wire:navigate>{{ __('Logistics') }}</a>
    <a class="rpt-tab {{ $activeTab === 'clients' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $activeTab === 'clients' ? 'true' : 'false' }}" href="{{ route('reports.index', array_merge(request()->query(), ['tab' => 'clients', 'range' => $activeRange])) }}" wire:navigate>{{ __('Clients') }}</a>
    <a class="rpt-tab {{ $activeTab === 'invoices' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $activeTab === 'invoices' ? 'true' : 'false' }}" href="{{ route('reports.index', array_merge(request()->query(), ['tab' => 'invoices', 'range' => $activeRange])) }}" wire:navigate>{{ __('Invoices') }}</a>
    <a class="rpt-tab {{ $activeTab === 'inventory' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $activeTab === 'inventory' ? 'true' : 'false' }}" href="{{ route('reports.index', array_merge(request()->query(), ['tab' => 'inventory', 'range' => $activeRange])) }}" wire:navigate>{{ __('Inventory') }}</a>
    <a class="rpt-tab {{ $activeTab === 'pnl' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $activeTab === 'pnl' ? 'true' : 'false' }}" href="{{ route('reports.index', array_merge(request()->query(), ['tab' => 'pnl', 'range' => $activeRange])) }}" wire:navigate>{{ __('Profit & Loss') }}</a>
    <a class="rpt-tab {{ $activeTab === 'expenses' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $activeTab === 'expenses' ? 'true' : 'false' }}" href="{{ route('reports.index', array_merge(request()->query(), ['tab' => 'expenses', 'range' => $activeRange])) }}" wire:navigate>{{ __('Expenses') }}</a>
    <a class="rpt-tab {{ $activeTab === 'capital' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $activeTab === 'capital' ? 'true' : 'false' }}" href="{{ route('reports.index', array_merge(request()->query(), ['tab' => 'capital', 'range' => $activeRange])) }}" wire:navigate>{{ __('Capital & Inventory') }}</a>
    <a class="rpt-tab {{ $activeTab === 'trend' ? 'is-active' : '' }}" role="tab" aria-selected="{{ $activeTab === 'trend' ? 'true' : 'false' }}" href="{{ route('reports.index', array_merge(request()->query(), ['tab' => 'trend', 'range' => $activeRange])) }}" wire:navigate>{{ __('Trend') }}</a>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- TAB: Overview                                                      --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="rpt-panel {{ $activeTab === 'overview' ? 'is-active' : '' }}" id="panel-overview" role="tabpanel">

    <div class="dash-card" style="margin-bottom:16px;padding:16px 20px;border-left:4px solid {{ $r['net_profit'] >= 0 ? '#15803d' : '#dc2626' }};">
        <div style="display:flex;flex-wrap:wrap;gap:20px;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:.78rem;color:var(--dash-muted);text-transform:uppercase;font-weight:700;letter-spacing:.05em;">{{ __('Revenue') }}</div>
                <div style="font-size:1.3rem;font-weight:900;color:#0369a1;">{{ $fmt($r['total_revenue']) }} TZS</div>
            </div>
            <div>
                <div style="font-size:.78rem;color:var(--dash-muted);text-transform:uppercase;font-weight:700;letter-spacing:.05em;">{{ __('Net profit') }}</div>
                <div style="font-size:1.3rem;font-weight:900;color:{{ $r['net_profit'] >= 0 ? '#15803d' : '#dc2626' }};">{{ $fmt($r['net_profit']) }} TZS</div>
            </div>
        </div>
    </div>

    {{-- KPI cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;letter-spacing:.06em;">{{ __('Total revenue') }}</div>
            <div style="font-size:1.6rem;font-weight:900;color:#0369a1;margin-top:6px;">{{ $fmt($r['total_revenue']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
            <div style="font-size:.8rem;color:var(--dash-muted);margin-top:4px;">{{ $r['sales_count'] }} {{ __('sales') }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;letter-spacing:.06em;">{{ __('Cost of goods sold') }}</div>
            <div style="font-size:1.6rem;font-weight:900;color:#b45309;margin-top:6px;">{{ $fmt($r['cogs']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
            <div style="font-size:.8rem;color:var(--dash-muted);margin-top:4px;">{{ __('Based on inventory buying price per unit') }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;letter-spacing:.06em;">{{ __('Gross profit') }}</div>
            <div style="font-size:1.6rem;font-weight:900;color:{{ $r['gross_profit'] >= 0 ? '#15803d' : 'var(--dash-danger)' }};margin-top:6px;">{{ $fmt($r['gross_profit']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
            <div style="font-size:.8rem;color:var(--dash-muted);margin-top:4px;">{{ __('Revenue − COGS') }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;letter-spacing:.06em;">{{ __('Total expenses') }}</div>
            <div style="font-size:1.6rem;font-weight:900;color:#dc2626;margin-top:6px;">{{ $fmt($r['total_expenses']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
            <div style="font-size:.8rem;color:var(--dash-muted);margin-top:4px;">{{ $r['expense_rows']->count() }} {{ __('categories') }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;border-left:4px solid {{ $r['net_profit'] >= 0 ? '#15803d' : '#dc2626' }};">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;letter-spacing:.06em;">{{ __('Net profit / loss') }}</div>
            <div style="font-size:1.6rem;font-weight:900;color:{{ $r['net_profit'] >= 0 ? '#15803d' : '#dc2626' }};margin-top:6px;">{{ $fmt($r['net_profit']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
            <div style="font-size:.8rem;color:var(--dash-muted);margin-top:4px;">
                {{ __('Total sales - buying price (COGS) - expenses') }}
            </div>
        </div>
    </div>

    {{-- Quick P&L summary --}}
    <div class="dash-card" style="margin-bottom:0;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Quick summary') }}</div>
                <div class="dash-card-subtitle">{{ $range['from_display'] }} — {{ $range['to_display'] }}</div>
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            <table style="width:100%;border-collapse:collapse;font-size:.9rem;">
                <tbody>
                    <tr style="border-bottom:1px solid var(--dash-border);">
                        <td style="padding:10px 0;font-weight:600;">{{ __('Total revenue') }}</td>
                        <td style="padding:10px 0;text-align:right;font-weight:600;color:#0369a1;">{{ $fmt($r['total_revenue']) }} TZS</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--dash-border);">
                        <td style="padding:10px 0;">{{ __('Cost of goods sold') }}</td>
                        <td style="padding:10px 0;text-align:right;color:#b45309;">−{{ $fmt($r['cogs']) }} TZS</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--dash-border);">
                        <td style="padding:10px 0;">{{ __('Total expenses') }}</td>
                        <td style="padding:10px 0;text-align:right;color:#dc2626;">−{{ $fmt($r['total_expenses']) }} TZS</td>
                    </tr>
                    <tr style="background:{{ $r['net_profit'] >= 0 ? '#f0fdf4' : '#fef2f2' }};">
                        <td style="padding:12px 0;font-weight:900;font-size:1rem;">
                            @if($r['net_profit'] >= 0) {{ __('NET PROFIT') }} @else {{ __('NET LOSS') }} @endif
                            <div style="font-size:.75rem;font-weight:600;color:var(--dash-muted);margin-top:2px;">{{ __('(Total sales - buying price - expenses)') }}</div>
                        </td>
                        <td style="padding:12px 0;text-align:right;font-weight:900;font-size:1.1rem;color:{{ $r['net_profit'] >= 0 ? '#15803d' : '#dc2626' }};">{{ $fmt($r['net_profit']) }} TZS</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- TAB: Sales                                                         --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="rpt-panel {{ $activeTab === 'sales' ? 'is-active' : '' }}" id="panel-sales" role="tabpanel">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;letter-spacing:.06em;">{{ __('Sales revenue') }}</div>
            <div style="font-size:1.6rem;font-weight:900;color:#0369a1;margin-top:6px;">{{ $fmt($r['total_revenue']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;letter-spacing:.06em;">{{ __('Sales count') }}</div>
            <div style="font-size:1.6rem;font-weight:900;color:var(--dash-ink);margin-top:6px;">{{ $r['sales_count'] }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;letter-spacing:.06em;">{{ __('Gross profit') }}</div>
            <div style="font-size:1.6rem;font-weight:900;color:{{ $r['gross_profit'] >= 0 ? '#15803d' : '#dc2626' }};margin-top:6px;">{{ $fmt($r['gross_profit']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:24px;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Top clients by sales') }}</div>
                <div class="dash-card-subtitle">{{ $range['from_display'] }} — {{ $range['to_display'] }}</div>
            </div>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Client') }}</th>
                        <th style="text-align:center;">{{ __('Sales count') }}</th>
                        <th style="text-align:right;">{{ __('Total sales') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($r['top_clients_by_sales'] as $row)
                    <tr>
                        <td><span class="dash-td-main">{{ $row->client_label ?: 'Walk-in' }}</span></td>
                        <td style="text-align:center;"><span class="dash-td-sub">{{ $row->sales_count }}</span></td>
                        <td style="text-align:right;"><span class="dash-td-amount" style="color:#0369a1;">{{ $fmt($row->total_sales) }} TZS</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center;padding:24px;color:var(--dash-muted);">{{ __('No sales in this period.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:0;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Most profitable sales') }}</div>
                <div class="dash-card-subtitle">{{ __('Based on sale total minus buying cost (COGS)') }}</div>
            </div>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Receipt') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th style="text-align:right;">{{ __('Sale amount') }}</th>
                        <th style="text-align:right;">{{ __('COGS') }}</th>
                        <th style="text-align:right;">{{ __('Profit') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($r['high_profit_sales'] as $row)
                    <tr>
                        <td><span class="dash-td-main">{{ $row->receipt_number ?? ('#' . $row->sale_id) }}</span></td>
                        <td><span class="dash-td-sub">{{ \Carbon\Carbon::parse($row->sale_date)->format('d M Y') }}</span></td>
                        <td style="text-align:right;"><span class="dash-td-amount">{{ $fmt($row->total) }} TZS</span></td>
                        <td style="text-align:right;"><span class="dash-td-amount" style="color:#b45309;">{{ $fmt($row->cogs) }} TZS</span></td>
                        <td style="text-align:right;"><span class="dash-td-amount" style="font-weight:700;color:{{ $row->profit >= 0 ? '#15803d' : '#dc2626' }};">{{ $fmt($row->profit) }} TZS</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:24px;color:var(--dash-muted);">{{ __('No profitable sales data in this period.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- TAB: Logistics                                                     --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="rpt-panel {{ $activeTab === 'logistics' ? 'is-active' : '' }}" id="panel-logistics" role="tabpanel">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Total shipments') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:var(--dash-ink);margin-top:6px;">{{ $r['logistics_totals']['total_shipments'] }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Delivery sales') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:#0369a1;margin-top:6px;">{{ $r['logistics_totals']['delivery_sales_count'] }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Cargo shipments') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:#7c3aed;margin-top:6px;">{{ $r['logistics_totals']['cargo_shipments_count'] }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Logistics revenue') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:#15803d;margin-top:6px;">{{ $fmt($r['logistics_totals']['delivery_sales_revenue'] + $r['logistics_totals']['cargo_revenue']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:0;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Shipment status summary') }}</div>
                <div class="dash-card-subtitle">{{ $range['from_display'] }} — {{ $range['to_display'] }}</div>
            </div>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Pending') }}</th>
                        <th>{{ __('In transit') }}</th>
                        <th>{{ __('Arrived') }}</th>
                        <th>{{ __('Received') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="dash-td-main">{{ $r['logistics_totals']['pending'] }}</span></td>
                        <td><span class="dash-td-main">{{ $r['logistics_totals']['in_transit'] }}</span></td>
                        <td><span class="dash-td-main">{{ $r['logistics_totals']['arrived'] }}</span></td>
                        <td><span class="dash-td-main">{{ $r['logistics_totals']['received'] }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- TAB: Clients                                                       --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="rpt-panel {{ $activeTab === 'clients' ? 'is-active' : '' }}" id="panel-clients" role="tabpanel">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Total clients') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:var(--dash-ink);margin-top:6px;">{{ $r['client_totals']['total_clients'] }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Active clients') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:#0369a1;margin-top:6px;">{{ $r['client_totals']['active_clients'] }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('New clients in period') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:#15803d;margin-top:6px;">{{ $r['client_totals']['new_clients'] }}</div>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:24px;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Top clients by sales') }}</div>
                <div class="dash-card-subtitle">{{ __('Highest sales totals in selected period') }}</div>
            </div>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Client') }}</th>
                        <th style="text-align:center;">{{ __('Sales count') }}</th>
                        <th style="text-align:right;">{{ __('Total sales') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($r['top_clients_by_sales'] as $row)
                    <tr>
                        <td><span class="dash-td-main">{{ $row->client_label ?: 'Walk-in' }}</span></td>
                        <td style="text-align:center;"><span class="dash-td-sub">{{ $row->sales_count }}</span></td>
                        <td style="text-align:right;"><span class="dash-td-amount">{{ $fmt($row->total_sales) }} TZS</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--dash-muted);">{{ __('No client sales data in this period.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:0;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Top clients by profit') }}</div>
                <div class="dash-card-subtitle">{{ __('Sales total minus buying cost (COGS)') }}</div>
            </div>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Client') }}</th>
                        <th style="text-align:center;">{{ __('Sales count') }}</th>
                        <th style="text-align:right;">{{ __('Total sales') }}</th>
                        <th style="text-align:right;">{{ __('Profit') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($r['top_clients_by_profit'] as $row)
                    <tr>
                        <td><span class="dash-td-main">{{ $row->client_label ?: 'Walk-in' }}</span></td>
                        <td style="text-align:center;"><span class="dash-td-sub">{{ $row->sales_count }}</span></td>
                        <td style="text-align:right;"><span class="dash-td-amount">{{ $fmt($row->total_sales) }} TZS</span></td>
                        <td style="text-align:right;"><span class="dash-td-amount" style="font-weight:700;color:{{ $row->profit >= 0 ? '#15803d' : '#dc2626' }};">{{ $fmt($row->profit) }} TZS</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--dash-muted);">{{ __('No client profit data in this period.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- TAB: Invoices                                                      --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="rpt-panel {{ $activeTab === 'invoices' ? 'is-active' : '' }}" id="panel-invoices" role="tabpanel">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Total invoices') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:var(--dash-ink);margin-top:6px;">{{ $r['invoice_totals']['total_count'] }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Issued total') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:#0369a1;margin-top:6px;">{{ $fmt($r['invoice_totals']['issued_total']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Paid total') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:#15803d;margin-top:6px;">{{ $fmt($r['invoice_totals']['paid_total']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;border-left:4px solid #dc2626;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Outstanding') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:#dc2626;margin-top:6px;">{{ $fmt($r['invoice_totals']['outstanding_total']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:24px;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Invoice status summary') }}</div>
                <div class="dash-card-subtitle">{{ __('Draft, sent, paid') }}</div>
            </div>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Draft') }}</th>
                        <th>{{ __('Sent') }}</th>
                        <th>{{ __('Paid') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="dash-td-main">{{ $r['invoice_totals']['draft_count'] }}</span></td>
                        <td><span class="dash-td-main">{{ $r['invoice_totals']['sent_count'] }}</span></td>
                        <td><span class="dash-td-main">{{ $r['invoice_totals']['paid_count'] }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:0;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Largest invoices') }}</div>
                <div class="dash-card-subtitle">{{ $range['from_display'] }} — {{ $range['to_display'] }}</div>
            </div>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Invoice') }}</th>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th style="text-align:right;">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($r['largest_invoices'] as $inv)
                    <tr>
                        <td><span class="dash-td-main">{{ $inv->display_number }}</span></td>
                        <td><span class="dash-td-sub">{{ $inv->client?->name ?? '—' }}</span></td>
                        <td><span class="dash-pill">{{ ucfirst($inv->status) }}</span></td>
                        <td style="text-align:right;"><span class="dash-td-amount">{{ $fmt($inv->total) }} TZS</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--dash-muted);">{{ __('No invoices in this period.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- TAB: Inventory                                                     --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="rpt-panel {{ $activeTab === 'inventory' ? 'is-active' : '' }}" id="panel-inventory" role="tabpanel">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Items in stock') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:var(--dash-ink);margin-top:6px;">{{ $r['inventory_totals']['items_in_stock'] }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Stock available') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:#0f766e;margin-top:6px;">{{ number_format((float) $r['inventory_totals']['stock_available_qty'], 2) }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Low stock items') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:#dc2626;margin-top:6px;">{{ $r['inventory_totals']['low_stock_count'] }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Stock cost') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:#b45309;margin-top:6px;">{{ $fmt($r['inventory_stock_cost']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;">{{ __('Stock value') }}</div>
            <div style="font-size:1.45rem;font-weight:900;color:#0369a1;margin-top:6px;">{{ $fmt($r['inventory_totals']['stock_worth']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:24px;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Low stock alert') }}</div>
                <div class="dash-card-subtitle">{{ __('Items with quantity 5 or below') }}</div>
            </div>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Store') }}</th>
                        <th style="text-align:right;">{{ __('Qty') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($r['low_stock_items'] as $inv)
                    <tr>
                        <td><span class="dash-td-main">{{ $inv->product?->name ?? '—' }}</span></td>
                        <td><span class="dash-td-sub">{{ $inv->store?->name ?? '—' }}</span></td>
                        <td style="text-align:right;">
                            <span class="dash-td-amount" style="color:#dc2626;">
                                {{ number_format((float) $inv->quantity, 2) }}
                                {{ $inv->product?->unit ? ' ' . $inv->product->unit : '' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--dash-muted);">{{ __('No low stock items currently.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="dash-card" style="margin-bottom:0;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Top inventory by value') }}</div>
                <div class="dash-card-subtitle">{{ __('Highest stock value items') }}</div>
            </div>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Store') }}</th>
                        <th style="text-align:right;">{{ __('Available') }}</th>
                        <th style="text-align:right;">{{ __('Stock value') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($r['top_inventory_value'] as $inv)
                    <tr>
                        <td><span class="dash-td-main">{{ $inv->product?->name ?? '—' }}</span></td>
                        <td><span class="dash-td-sub">{{ $inv->store?->name ?? '—' }}</span></td>
                        <td style="text-align:right;">
                            <span class="dash-td-sub">
                                {{ number_format((float) $inv->quantity, 2) }}
                                {{ $inv->product?->unit ? ' ' . $inv->product->unit : '' }}
                            </span>
                        </td>
                        <td style="text-align:right;"><span class="dash-td-amount">{{ $fmt($inv->stock_value) }} TZS</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--dash-muted);">{{ __('No inventory items available.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- TAB: Profit & Loss                                                 --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="rpt-panel {{ $activeTab === 'pnl' ? 'is-active' : '' }}" id="panel-pnl" role="tabpanel">
    <div class="dash-card" style="margin-bottom:0;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Profit & Loss statement') }}</div>
                <div class="dash-card-subtitle">{{ $range['from_display'] }} — {{ $range['to_display'] }}</div>
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            <table style="width:100%;border-collapse:collapse;font-size:.9rem;">
                <tbody>
                    <tr style="border-bottom:1px solid var(--dash-border);">
                        <td style="padding:10px 0;font-weight:600;">{{ __('Sales revenue') }}</td>
                        <td style="padding:10px 0;text-align:right;">{{ $fmt($r['total_subtotal']) }} TZS</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--dash-border);">
                        <td style="padding:10px 0;color:var(--dash-muted);padding-left:16px;">{{ __('+ Delivery charges collected') }}</td>
                        <td style="padding:10px 0;text-align:right;color:var(--dash-muted);">{{ $fmt($r['total_delivery_cost']) }} TZS</td>
                    </tr>
                    <tr style="border-bottom:2px solid var(--dash-border);background:#f8fafb;">
                        <td style="padding:10px 0;font-weight:700;">{{ __('Total revenue') }}</td>
                        <td style="padding:10px 0;text-align:right;font-weight:700;">{{ $fmt($r['total_revenue']) }} TZS</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--dash-border);">
                        <td style="padding:10px 0;color:#b45309;font-weight:600;">{{ __('Cost of goods sold (COGS)') }}</td>
                        <td style="padding:10px 0;text-align:right;color:#b45309;">−{{ $fmt($r['cogs']) }} TZS</td>
                    </tr>
                    <tr style="border-bottom:2px solid var(--dash-border);background:#f0fdf4;">
                        <td style="padding:10px 0;font-weight:700;color:#15803d;">{{ __('Gross profit') }}</td>
                        <td style="padding:10px 0;text-align:right;font-weight:700;color:{{ $r['gross_profit'] >= 0 ? '#15803d' : '#dc2626' }};">{{ $fmt($r['gross_profit']) }} TZS</td>
                    </tr>
                    @foreach($r['expense_rows'] as $cat)
                    <tr style="border-bottom:1px solid var(--dash-border);">
                        <td style="padding:10px 0;color:var(--dash-muted);padding-left:16px;">{{ $cat->category_name }} <span style="font-size:.75rem;">({{ $cat->count }})</span></td>
                        <td style="padding:10px 0;text-align:right;color:var(--dash-muted);">−{{ number_format((float) $cat->total, 0) }} TZS</td>
                    </tr>
                    @endforeach
                    <tr style="border-bottom:2px solid var(--dash-border);background:#fef2f2;">
                        <td style="padding:10px 0;font-weight:700;color:#dc2626;">{{ __('Total expenses') }}</td>
                        <td style="padding:10px 0;text-align:right;font-weight:700;color:#dc2626;">−{{ $fmt($r['total_expenses']) }} TZS</td>
                    </tr>
                    <tr style="background:{{ $r['net_profit'] >= 0 ? '#f0fdf4' : '#fef2f2' }};">
                        <td style="padding:12px 0;font-weight:900;font-size:1rem;">
                            @if($r['net_profit'] >= 0) {{ __('NET PROFIT') }} @else {{ __('NET LOSS') }} @endif
                            <div style="font-size:.75rem;font-weight:600;color:var(--dash-muted);margin-top:2px;">{{ __('(Total sales - buying price - expenses)') }}</div>
                        </td>
                        <td style="padding:12px 0;text-align:right;font-weight:900;font-size:1.1rem;color:{{ $r['net_profit'] >= 0 ? '#15803d' : '#dc2626' }};">{{ $fmt($r['net_profit']) }} TZS</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- TAB: Expenses                                                      --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="rpt-panel {{ $activeTab === 'expenses' ? 'is-active' : '' }}" id="panel-expenses" role="tabpanel">

    {{-- Expenses KPI --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
        <div class="dash-card" style="margin-bottom:0;padding:18px;border-left:4px solid #dc2626;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;letter-spacing:.06em;">{{ __('Total expenses') }}</div>
            <div style="font-size:1.6rem;font-weight:900;color:#dc2626;margin-top:6px;">{{ $fmt($r['total_expenses']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;letter-spacing:.06em;">{{ __('Categories') }}</div>
            <div style="font-size:1.6rem;font-weight:900;color:var(--dash-ink);margin-top:6px;">{{ $r['expense_rows']->count() }}</div>
        </div>
    </div>

    {{-- Expenses by category --}}
    <div class="dash-card" style="margin-bottom:24px;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Expenses by category') }}</div>
                <div class="dash-card-subtitle">{{ $range['from_display'] }} — {{ $range['to_display'] }}</div>
            </div>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Category') }}</th>
                        <th style="text-align:center;">{{ __('Count') }}</th>
                        <th style="text-align:right;">{{ __('Total') }}</th>
                        <th style="text-align:right;">{{ __('% of total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($r['expense_rows'] as $cat)
                    <tr>
                        <td><span class="dash-td-main">{{ $cat->category_name }}</span></td>
                        <td style="text-align:center;"><span class="dash-td-sub">{{ $cat->count }}</span></td>
                        <td style="text-align:right;"><span class="dash-td-amount" style="color:#dc2626;">{{ number_format((float) $cat->total, 0) }} TZS</span></td>
                        <td style="text-align:right;">
                            @php $pct = $r['total_expenses'] > 0 ? round(((float) $cat->total / $r['total_expenses']) * 100, 1) : 0; @endphp
                            <span class="dash-td-sub">{{ $pct }}%</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:24px;color:var(--dash-muted);">{{ __('No expenses in this period.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($r['expense_rows']->isNotEmpty())
                <tfoot>
                    <tr style="font-weight:700;background:#fef2f2;">
                        <td style="padding:10px;">{{ __('Total') }}</td>
                        <td style="text-align:center;padding:10px;">{{ $r['expense_rows']->sum('count') }}</td>
                        <td style="text-align:right;padding:10px;color:#dc2626;">{{ $fmt($r['total_expenses']) }} TZS</td>
                        <td style="text-align:right;padding:10px;">100%</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Expense category bar --}}
    @if($r['expense_rows']->isNotEmpty())
    @php $maxExp = max(1, (float) $r['expense_rows']->max('total')); @endphp
    <div class="dash-card" style="margin-bottom:0;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Expense breakdown') }}</div>
                <div class="dash-card-subtitle">{{ __('Visual — by category') }}</div>
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            @foreach($r['expense_rows'] as $cat)
            @php $catPct = ($maxExp > 0) ? (((float) $cat->total / $maxExp) * 100) : 0; @endphp
            <div style="margin-bottom:12px;">
                <div style="display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:4px;">
                    <span style="font-weight:600;color:var(--dash-ink);">{{ $cat->category_name }}</span>
                    <span style="color:#dc2626;font-weight:600;">{{ number_format((float) $cat->total, 0) }} TZS</span>
                </div>
                <div style="height:14px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
                    <div style="height:100%;width:{{ min($catPct, 100) }}%;background:#dc2626;border-radius:4px;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- TAB: Capital & Inventory                                           --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="rpt-panel {{ $activeTab === 'capital' ? 'is-active' : '' }}" id="panel-capital" role="tabpanel">
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;letter-spacing:.06em;">{{ __('Stock orders (period)') }}</div>
            <div style="font-size:1.35rem;font-weight:900;color:#b45309;margin-top:6px;">{{ $fmt($r['stock_orders_total']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
            <div style="font-size:.8rem;color:var(--dash-muted);margin-top:4px;">{{ $r['stock_orders_count'] }} {{ __('orders') }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;letter-spacing:.06em;">{{ __('Current inventory cost') }}</div>
            <div style="font-size:1.35rem;font-weight:900;color:#b45309;margin-top:6px;">{{ $fmt($r['inventory_stock_cost']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
            <div style="font-size:.8rem;color:var(--dash-muted);margin-top:4px;">{{ __('At buying price (all stores)') }}</div>
        </div>
        <div class="dash-card" style="margin-bottom:0;padding:18px;">
            <div style="font-size:.7rem;font-weight:700;color:var(--dash-muted);text-transform:uppercase;letter-spacing:.06em;">{{ __('Current inventory value') }}</div>
            <div style="font-size:1.35rem;font-weight:900;color:#0369a1;margin-top:6px;">{{ $fmt($r['inventory_stock_value']) }} <span style="font-size:.75rem;font-weight:500;">TZS</span></div>
            <div style="font-size:.8rem;color:var(--dash-muted);margin-top:4px;">{{ __('At selling price (all stores)') }}</div>
        </div>
    </div>

    @php $potentialMargin = $r['inventory_stock_value'] - $r['inventory_stock_cost']; @endphp
    <div class="dash-card" style="margin-bottom:0;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Capital analysis') }}</div>
                <div class="dash-card-subtitle">{{ __('Investment vs potential return on current stock') }}</div>
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            <table style="width:100%;border-collapse:collapse;font-size:.9rem;">
                <tbody>
                    <tr style="border-bottom:1px solid var(--dash-border);">
                        <td style="padding:10px 0;font-weight:600;">{{ __('Stock orders placed (period)') }}</td>
                        <td style="padding:10px 0;text-align:right;">{{ $fmt($r['stock_orders_total']) }} TZS</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--dash-border);">
                        <td style="padding:10px 0;font-weight:600;">{{ __('Capital tied in current inventory') }}</td>
                        <td style="padding:10px 0;text-align:right;color:#b45309;">{{ $fmt($r['inventory_stock_cost']) }} TZS</td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--dash-border);">
                        <td style="padding:10px 0;font-weight:600;">{{ __('Inventory value if sold') }}</td>
                        <td style="padding:10px 0;text-align:right;color:#0369a1;">{{ $fmt($r['inventory_stock_value']) }} TZS</td>
                    </tr>
                    <tr style="background:{{ $potentialMargin >= 0 ? '#f0fdf4' : '#fef2f2' }};">
                        <td style="padding:12px 0;font-weight:900;">{{ __('Potential margin on stock') }}</td>
                        <td style="padding:12px 0;text-align:right;font-weight:900;color:{{ $potentialMargin >= 0 ? '#15803d' : '#dc2626' }};">{{ $fmt($potentialMargin) }} TZS</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- TAB: Trend                                                         --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="rpt-panel {{ $activeTab === 'trend' ? 'is-active' : '' }}" id="panel-trend" role="tabpanel">

    {{-- Monthly trend table --}}
    <div class="dash-card" style="margin-bottom:24px;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('6-month trend') }}</div>
                <div class="dash-card-subtitle">{{ __('Revenue vs expenses vs net profit/loss per month') }}</div>
            </div>
        </div>
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Month') }}</th>
                        <th style="text-align:right;">{{ __('Revenue') }}</th>
                        <th style="text-align:right;">{{ __('COGS') }}</th>
                        <th style="text-align:right;">{{ __('Expenses') }}</th>
                        <th style="text-align:right;">{{ __('Net') }}</th>
                        <th style="text-align:right;">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($r['trend'] as $m)
                    <tr>
                        <td><span class="dash-td-main">{{ $m['label'] }}</span></td>
                        <td style="text-align:right;"><span class="dash-td-amount" style="color:#0369a1;">{{ $fmt($m['revenue']) }}</span></td>
                        <td style="text-align:right;"><span class="dash-td-amount" style="color:#b45309;">{{ $fmt($m['cogs']) }}</span></td>
                        <td style="text-align:right;"><span class="dash-td-amount" style="color:#dc2626;">{{ $fmt($m['expenses']) }}</span></td>
                        <td style="text-align:right;"><span class="dash-td-amount" style="font-weight:700;color:{{ $m['net'] >= 0 ? '#15803d' : '#dc2626' }};">{{ $fmt($m['net']) }}</span></td>
                        <td style="text-align:right;">
                            @if($m['net'] > 0)
                                <span class="dash-pill dash-pill-green">{{ __('Profit') }}</span>
                            @elseif($m['net'] < 0)
                                <span class="dash-pill dash-pill-red">{{ __('Loss') }}</span>
                            @else
                                <span class="dash-pill">{{ __('Break-even') }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Visual bar chart --}}
    @php
        $maxVal = max(1, max(array_column($r['trend'], 'revenue')), max(array_column($r['trend'], 'expenses')));
    @endphp
    <div class="dash-card" style="margin-bottom:0;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Revenue vs Expenses') }}</div>
                <div class="dash-card-subtitle">{{ __('Visual comparison — last 6 months') }}</div>
            </div>
        </div>
        <div style="padding:0 20px 20px;">
            <div style="display:flex;gap:12px;margin-bottom:16px;">
                <span style="display:inline-flex;align-items:center;gap:6px;font-size:.75rem;color:var(--dash-muted);"><span style="width:12px;height:12px;border-radius:3px;background:#0369a1;display:inline-block;"></span> {{ __('Revenue') }}</span>
                <span style="display:inline-flex;align-items:center;gap:6px;font-size:.75rem;color:var(--dash-muted);"><span style="width:12px;height:12px;border-radius:3px;background:#dc2626;display:inline-block;"></span> {{ __('Expenses + COGS') }}</span>
            </div>
            @foreach($r['trend'] as $m)
            @php
                $revPct = $maxVal > 0 ? (($m['revenue'] / $maxVal) * 100) : 0;
                $expPct = $maxVal > 0 ? ((($m['expenses'] + $m['cogs']) / $maxVal) * 100) : 0;
            @endphp
            <div style="margin-bottom:14px;">
                <div style="font-size:.8rem;font-weight:600;margin-bottom:4px;color:var(--dash-ink);">{{ $m['label'] }}</div>
                <div style="display:flex;gap:6px;align-items:center;">
                    <div style="flex:1;height:16px;background:#f1f5f9;border-radius:4px;overflow:hidden;position:relative;">
                        <div style="position:absolute;top:0;left:0;height:100%;width:{{ min($revPct, 100) }}%;background:#0369a1;border-radius:4px;"></div>
                    </div>
                    <span style="font-size:.75rem;color:#0369a1;width:80px;text-align:right;flex-shrink:0;">{{ $fmt($m['revenue']) }}</span>
                </div>
                <div style="display:flex;gap:6px;align-items:center;margin-top:3px;">
                    <div style="flex:1;height:12px;background:#f1f5f9;border-radius:4px;overflow:hidden;position:relative;">
                        <div style="position:absolute;top:0;left:0;height:100%;width:{{ min($expPct, 100) }}%;background:#dc2626;border-radius:4px;"></div>
                    </div>
                    <span style="font-size:.75rem;color:#dc2626;width:80px;text-align:right;flex-shrink:0;">{{ $fmt($m['expenses'] + $m['cogs']) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

