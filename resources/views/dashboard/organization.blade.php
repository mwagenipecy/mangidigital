@extends('layouts.dashboard')

@section('title', __('Dashboard'))

@section('content')
@php
    $user = auth()->user();
    $firstName = $user ? collect(explode(' ', $user->name))->first() : 'User';
    $greeting = now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening');
@endphp
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">{{ $greeting }}, <em>{{ $firstName }}</em></h1>
        <p class="dash-page-subtitle">{{ $organization->name }} — {{ now()->format('l, j F Y') }}</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <a href="{{ route('sales.create') }}" class="dash-btn dash-btn-brand">
            <flux:icon.plus class="size-4" />
            New sale
        </a>
    </div>
</div>

<div class="dash-kpi-grid">
    <x-dashboard.kpi-card :value="number_format($salesTotal, 0)" label="Total sales (TZS)" :trend="'This month: ' . number_format($salesThisMonth, 0)" :trendUp="true" color="var(--dash-brand)" bg="var(--dash-brand-10)">
        <x-slot:icon><flux:icon.chart-bar class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="number_format($expensesTotal, 0)" label="Expenses (TZS)" :trend="'This month: ' . number_format($expensesThisMonth, 0)" :trendUp="false" color="var(--dash-danger)" bg="rgba(239,68,68,.08)">
        <x-slot:icon><flux:icon.banknotes class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="number_format($salesCount)" label="Total orders" :trend="'This month: ' . $salesThisMonthCount" :trendUp="true" color="var(--dash-ok)" bg="rgba(34,197,94,.08)">
        <x-slot:icon><flux:icon.shopping-cart class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="number_format($clientsCount)" label="Clients" color="var(--dash-warn)" bg="rgba(245,158,11,.08)">
        <x-slot:icon><flux:icon.users class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
</div>

<div class="dash-kpi-grid dash-kpi-grid--three">
    <x-dashboard.kpi-card :value="(string)$inventoryCount" label="In-stock items" color="var(--dash-ok)" bg="rgba(34,197,94,.08)">
        <x-slot:icon><flux:icon.cube class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="(string)$stockOrdersInTransit" label="Stock in transit" color="var(--dash-warn)" bg="rgba(245,158,11,.08)">
        <x-slot:icon><flux:icon.truck class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card :value="(string)$deliveriesPending" label="Deliveries pending" color="var(--dash-purple)" bg="rgba(139,92,246,.1)">
        <x-slot:icon><flux:icon.map-pin class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
</div>

<div class="dash-qa-grid">
    <x-dashboard.quick-action-card :href="route('sales.create')" label="New sale" sub="Record a sale">
        <x-slot:icon><flux:icon.plus class="size-6" /></x-slot:icon>
    </x-dashboard.quick-action-card>
    <x-dashboard.quick-action-card :href="route('expenses.create')" label="Add expense" sub="Log a business cost">
        <x-slot:icon><flux:icon.banknotes class="size-6" /></x-slot:icon>
    </x-dashboard.quick-action-card>
    <x-dashboard.quick-action-card :href="route('inventory.create')" label="Record inventory" sub="Add stock entry">
        <x-slot:icon><flux:icon.cube class="size-6" /></x-slot:icon>
    </x-dashboard.quick-action-card>
    <x-dashboard.quick-action-card :href="route('stock-orders.create')" label="Order stock" sub="Create stock order">
        <x-slot:icon><flux:icon.truck class="size-6" /></x-slot:icon>
    </x-dashboard.quick-action-card>
    <x-dashboard.quick-action-card :href="route('logistics.index')" label="Logistics" sub="Delivery status">
        <x-slot:icon><flux:icon.map-pin class="size-6" /></x-slot:icon>
    </x-dashboard.quick-action-card>
</div>

<div class="dash-charts-grid">
    <x-dashboard.card title="Sales vs expenses" subtitle="Last 7 days" actionLabel="View sales" :actionUrl="route('sales.index')">
        <div style="display:flex;gap:16px;align-items:center;margin-bottom:12px">
            <div style="display:flex;align-items:center;gap:6px;font-size:.75rem;color:var(--dash-muted)">
                <div style="width:10px;height:10px;border-radius:2px;background:var(--dash-brand)"></div>Sales
            </div>
            <div style="display:flex;align-items:center;gap:6px;font-size:.75rem;color:var(--dash-muted)">
                <div style="width:10px;height:10px;border-radius:2px;background:rgba(239,68,68,.35)"></div>Expenses
            </div>
        </div>
        <div class="dash-bar-chart" id="dashBarChart"></div>
        <div class="dash-bar-labels" id="dashBarLabels"></div>
    </x-dashboard.card>

    <x-dashboard.card title="Expense breakdown" subtitle="By category this month" actionLabel="Expenses" :actionUrl="route('expenses.index')">
        <div class="dash-donut-wrap">
            @php
                $expTotal = $expensesByCategory->sum('total');
                $donutColors = ['var(--dash-brand)', '#f59e0b', '#8b5cf6', '#22c55e', '#ef4444', '#06b6d4'];
            @endphp
            @if($expTotal > 0)
                <svg class="dash-donut-svg" viewBox="0 0 42 42" style="transform:rotate(-90deg)">
                    <circle cx="21" cy="21" r="15.915" fill="none" stroke="var(--dash-border)" stroke-width="4"/>
                    @php $offset = 0; $circum = 100; @endphp
                    @foreach($expensesByCategory as $i => $row)
                        @php
                            $pct = (float)$row->total / (float)$expTotal;
                            $seg = $pct * 100;
                            $color = $donutColors[$i % count($donutColors)];
                        @endphp
                        <circle cx="21" cy="21" r="15.915" fill="none" stroke="{{ $color }}" stroke-width="4" stroke-dasharray="{{ $seg }} {{ 100 - $seg }}" stroke-dashoffset="-{{ $offset }}" stroke-linecap="round"/>
                        @php $offset += $seg; @endphp
                    @endforeach
                </svg>
                <div class="dash-donut-legend">
                    @foreach($expensesByCategory as $i => $row)
                        @php
                            $pct = $expTotal > 0 ? round((float)$row->total / (float)$expTotal * 100) : 0;
                            $color = $donutColors[$i % count($donutColors)];
                        @endphp
                        <div class="dash-dl-item">
                            <div class="dash-dl-dot" style="background:{{ $color }}"></div>
                            <span class="dash-dl-label">{{ $row->category_name }}</span>
                            <span class="dash-dl-val">{{ $pct }}%</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="margin:0;color:var(--dash-muted);font-size:.9rem;">No expenses this month.</p>
            @endif
        </div>
    </x-dashboard.card>
</div>

<div class="dash-tables-grid">
    <x-dashboard.card title="Recent sales" subtitle="Latest 5" actionLabel="View all →" :actionUrl="route('sales.index')">
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Receipt</th>
                        <th>Client</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSales as $sale)
                    <tr>
                        <td>
                            <a href="{{ route('sales.show', $sale) }}" style="font-family:'Space Mono',monospace;font-size:.78rem;color:var(--dash-brand);text-decoration:underline;">
                                {{ $sale->receipt_number ?? '#' . $sale->id }}
                            </a>
                        </td>
                        <td>
                            <div class="dash-td-name">
                                <div class="dash-td-avatar">{{ Str::upper(Str::limit($sale->client_name ?: ($sale->client?->name ?? '—'), 2)) }}</div>
                                <div>
                                    <div class="dash-td-main">{{ $sale->client_name ?: ($sale->client?->name ?? '—') }}</div>
                                    @if($sale->client?->phone)
                                        <div class="dash-td-sub">{{ $sale->client->phone }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td><span class="dash-td-amount">{{ number_format($sale->total, 0) }}</span></td>
                        <td><span class="dash-td-sub">{{ $sale->sale_date?->format('d M Y') }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:24px;color:var(--dash-muted);">No sales yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-dashboard.card>

    <x-dashboard.card title="Quick links" subtitle="Shortcuts">
        <div style="display:flex;flex-direction:column;gap:8px;">
            <a href="{{ route('clients.index') }}" class="dash-btn dash-btn-outline" style="justify-content:flex-start;">
                <flux:icon.users class="size-4" /> Clients ({{ $clientsCount }})
            </a>
            <a href="{{ route('inventory.index') }}" class="dash-btn dash-btn-outline" style="justify-content:flex-start;">
                <flux:icon.cube class="size-4" /> Inventory
            </a>
            <a href="{{ route('stock-returns.index') }}" class="dash-btn dash-btn-outline" style="justify-content:flex-start;">
                <flux:icon.arrow-uturn-left class="size-4" /> Return stock
            </a>
        </div>
    </x-dashboard.card>
</div>

@push('scripts')
<script>
(function() {
  var data = @json($last7Days);
  if (!data || data.length === 0) return;
  var labels = data.map(function(d) { return d.label; });
  var sales = data.map(function(d) { return Number(d.sales) || 0; });
  var expenses = data.map(function(d) { return Number(d.expenses) || 0; });
  var maxVal = Math.max.apply(null, sales.concat(expenses), 1);
  var chart = document.getElementById('dashBarChart');
  var labelsEl = document.getElementById('dashBarLabels');
  if (!chart || !labelsEl) return;
  for (var i = 0; i < labels.length; i++) {
    var col = document.createElement('div');
    col.style.cssText = 'flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;min-width:0';
    var barPair = document.createElement('div');
    barPair.style.cssText = 'flex:1;display:flex;gap:2px;align-items:flex-end;width:100%;min-width:0';
    var sb = document.createElement('div');
    sb.className = 'dash-bc-bar';
    sb.style.cssText = 'height:' + (sales[i]/maxVal*100) + '%;background:var(--dash-brand);border-radius:5px 5px 0 0;flex:1;width:100%;position:relative;min-width:0';
    var eb = document.createElement('div');
    eb.className = 'dash-bc-bar';
    eb.style.cssText = 'height:' + (expenses[i]/maxVal*100) + '%;background:rgba(239,68,68,.35);border-radius:5px 5px 0 0;flex:none;width:100%;position:relative;min-width:0';
    barPair.appendChild(sb);
    barPair.appendChild(eb);
    chart.appendChild(barPair);
    var lbl = document.createElement('span');
    lbl.textContent = labels[i];
    labelsEl.appendChild(lbl);
  }
})();
</script>
@endpush
@endsection
