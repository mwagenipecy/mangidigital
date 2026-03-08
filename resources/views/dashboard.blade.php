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
        <p class="dash-page-subtitle">Here's what's happening with your business today — {{ now()->format('l, j F Y') }}</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <button type="button" class="dash-date-badge">
            <flux:icon.calendar-days class="size-4" />
            Mar 1 – Mar 8, 2025 ▾
        </button>
        <a href="#" class="dash-btn dash-btn-brand">
            <flux:icon.plus class="size-4" />
            New Order
        </a>
    </div>
</div>

<div class="dash-kpi-grid">
    <x-dashboard.kpi-card value="3.2M" label="Total Sales (TZS)" trend="↑ +21%" :trendUp="true" color="var(--dash-brand)" bg="var(--dash-brand-10)">
        <x-slot:icon><flux:icon.chart-bar class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card value="620K" label="Expenses (TZS)" trend="↑ +3%" :trendUp="false" color="var(--dash-danger)" bg="rgba(239,68,68,.08)">
        <x-slot:icon><flux:icon.banknotes class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card value="148" label="Total Orders" trend="↑ +12%" :trendUp="true" color="var(--dash-ok)" bg="rgba(34,197,94,.08)">
        <x-slot:icon><flux:icon.shopping-cart class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
    <x-dashboard.kpi-card value="342" label="Active Clients" trend="↑ +8" :trendUp="true" color="var(--dash-warn)" bg="rgba(245,158,11,.08)">
        <x-slot:icon><flux:icon.credit-card class="size-5" /></x-slot:icon>
    </x-dashboard.kpi-card>
</div>

<div class="dash-qa-grid">
    <x-dashboard.quick-action-card href="#" label="New Order" sub="Record incoming order">
        <x-slot:icon><flux:icon.plus class="size-6" /></x-slot:icon>
    </x-dashboard.quick-action-card>
    <x-dashboard.quick-action-card href="#" label="Record Payment" sub="Client instalment">
        <x-slot:icon><flux:icon.credit-card class="size-6" /></x-slot:icon>
    </x-dashboard.quick-action-card>
    <x-dashboard.quick-action-card href="#" label="Add Expense" sub="Log a business cost">
        <x-slot:icon><flux:icon.banknotes class="size-6" /></x-slot:icon>
    </x-dashboard.quick-action-card>
    <x-dashboard.quick-action-card href="#" label="Create Invoice" sub="Send to client">
        <x-slot:icon><flux:icon.document-text class="size-6" /></x-slot:icon>
    </x-dashboard.quick-action-card>
</div>

<div class="dash-charts-grid">
    <x-dashboard.card title="Sales Overview" subtitle="Revenue vs Expenses — Last 7 days" actionLabel="View all" actionUrl="#">
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

    <x-dashboard.card title="Expense Breakdown" subtitle="By category this month" actionLabel="Details" actionUrl="#">
        <div class="dash-donut-wrap">
            <svg class="dash-donut-svg" viewBox="0 0 42 42" style="transform:rotate(-90deg)">
                <circle cx="21" cy="21" r="15.915" fill="none" stroke="var(--dash-border)" stroke-width="4"/>
                <circle cx="21" cy="21" r="15.915" fill="none" stroke="var(--dash-brand)" stroke-width="4" stroke-dasharray="35 65" stroke-dashoffset="0" stroke-linecap="round"/>
                <circle cx="21" cy="21" r="15.915" fill="none" stroke="#f59e0b" stroke-width="4" stroke-dasharray="22 78" stroke-dashoffset="-35" stroke-linecap="round"/>
                <circle cx="21" cy="21" r="15.915" fill="none" stroke="#8b5cf6" stroke-width="4" stroke-dasharray="18 82" stroke-dashoffset="-57" stroke-linecap="round"/>
                <circle cx="21" cy="21" r="15.915" fill="none" stroke="#22c55e" stroke-width="4" stroke-dasharray="15 85" stroke-dashoffset="-75" stroke-linecap="round"/>
                <circle cx="21" cy="21" r="15.915" fill="none" stroke="#ef4444" stroke-width="4" stroke-dasharray="10 90" stroke-dashoffset="-90" stroke-linecap="round"/>
            </svg>
            <div class="dash-donut-legend">
                <div class="dash-dl-item"><div class="dash-dl-dot" style="background:var(--dash-brand)"></div><span class="dash-dl-label">Stock/Inventory</span><span class="dash-dl-val">35%</span></div>
                <div class="dash-dl-item"><div class="dash-dl-dot" style="background:#f59e0b"></div><span class="dash-dl-label">Operations</span><span class="dash-dl-val">22%</span></div>
                <div class="dash-dl-item"><div class="dash-dl-dot" style="background:#8b5cf6"></div><span class="dash-dl-label">Salaries</span><span class="dash-dl-val">18%</span></div>
                <div class="dash-dl-item"><div class="dash-dl-dot" style="background:#22c55e"></div><span class="dash-dl-label">Marketing</span><span class="dash-dl-val">15%</span></div>
                <div class="dash-dl-item"><div class="dash-dl-dot" style="background:#ef4444"></div><span class="dash-dl-label">Other</span><span class="dash-dl-val">10%</span></div>
            </div>
        </div>
    </x-dashboard.card>
</div>

<div class="dash-tables-grid">
    <x-dashboard.card title="Recent Orders" subtitle="Latest 5 orders" actionLabel="View all →" actionUrl="#">
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Client</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span style="font-family:'Space Mono',monospace;font-size:.78rem;color:var(--dash-brand)">#1044</span></td>
                        <td><div class="dash-td-name"><div class="dash-td-avatar">BM</div><div><div class="dash-td-main">Baraka Mart</div><div class="dash-td-sub">Today 09:14</div></div></div></td>
                        <td><span class="dash-td-amount">220,000</span></td>
                        <td><span class="dash-pill dash-pill-blue">Pending</span></td>
                    </tr>
                    <tr>
                        <td><span style="font-family:'Space Mono',monospace;font-size:.78rem;color:var(--dash-brand)">#1043</span></td>
                        <td><div class="dash-td-name"><div class="dash-td-avatar">JH</div><div><div class="dash-td-main">Juma Holdings</div><div class="dash-td-sub">Today 07:30</div></div></div></td>
                        <td><span class="dash-td-amount">150,000</span></td>
                        <td><span class="dash-pill dash-pill-green">Delivered</span></td>
                    </tr>
                    <tr>
                        <td><span style="font-family:'Space Mono',monospace;font-size:.78rem;color:var(--dash-brand)">#1042</span></td>
                        <td><div class="dash-td-name"><div class="dash-td-avatar">AT</div><div><div class="dash-td-main">Amina Traders</div><div class="dash-td-sub">Yesterday</div></div></div></td>
                        <td><span class="dash-td-amount">80,000</span></td>
                        <td><span class="dash-pill dash-pill-green">Delivered</span></td>
                    </tr>
                    <tr>
                        <td><span style="font-family:'Space Mono',monospace;font-size:.78rem;color:var(--dash-brand)">#1041</span></td>
                        <td><div class="dash-td-name"><div class="dash-td-avatar">FS</div><div><div class="dash-td-main">Fatuma Shop</div><div class="dash-td-sub">Yesterday</div></div></div></td>
                        <td><span class="dash-td-amount">340,000</span></td>
                        <td><span class="dash-pill dash-pill-yellow">In Transit</span></td>
                    </tr>
                    <tr>
                        <td><span style="font-family:'Space Mono',monospace;font-size:.78rem;color:var(--dash-brand)">#1040</span></td>
                        <td><div class="dash-td-name"><div class="dash-td-avatar">NK</div><div><div class="dash-td-main">Njema Kiosk</div><div class="dash-td-sub">Mar 6</div></div></div></td>
                        <td><span class="dash-td-amount">95,000</span></td>
                        <td><span class="dash-pill dash-pill-green">Delivered</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-dashboard.card>

    <x-dashboard.card title="Pending Payments" subtitle="Clients with outstanding balance" actionLabel="Send reminders" actionUrl="#">
        <div class="dash-table-wrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Balance</th>
                        <th>Due</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><div class="dash-td-name"><div class="dash-td-avatar" style="background:rgba(239,68,68,.1);color:var(--dash-danger);border-color:rgba(239,68,68,.2)">JH</div><div><div class="dash-td-main">Juma Holdings</div><div class="dash-td-sub">3 instalments left</div></div></div></td>
                        <td><span class="dash-td-amount neg">280,000</span></td>
                        <td><span class="dash-pill dash-pill-red">Overdue</span></td>
                    </tr>
                    <tr>
                        <td><div class="dash-td-name"><div class="dash-td-avatar">BM</div><div><div class="dash-td-main">Baraka Mart</div><div class="dash-td-sub">2 instalments left</div></div></div></td>
                        <td><span class="dash-td-amount" style="color:var(--dash-warn)">150,000</span></td>
                        <td><span class="dash-pill dash-pill-yellow">Mar 12</span></td>
                    </tr>
                    <tr>
                        <td><div class="dash-td-name"><div class="dash-td-avatar" style="background:rgba(239,68,68,.1);color:var(--dash-danger);border-color:rgba(239,68,68,.2)">FS</div><div><div class="dash-td-main">Fatuma Shop</div><div class="dash-td-sub">5 instalments left</div></div></div></td>
                        <td><span class="dash-td-amount neg">520,000</span></td>
                        <td><span class="dash-pill dash-pill-red">Overdue</span></td>
                    </tr>
                    <tr>
                        <td><div class="dash-td-name"><div class="dash-td-avatar">NK</div><div><div class="dash-td-main">Njema Kiosk</div><div class="dash-td-sub">1 instalment left</div></div></div></td>
                        <td><span class="dash-td-amount">45,000</span></td>
                        <td><span class="dash-pill dash-pill-blue">Mar 20</span></td>
                    </tr>
                    <tr>
                        <td><div class="dash-td-name"><div class="dash-td-avatar">RM</div><div><div class="dash-td-main">Raha Market</div><div class="dash-td-sub">4 instalments left</div></div></div></td>
                        <td><span class="dash-td-amount" style="color:var(--dash-warn)">200,000</span></td>
                        <td><span class="dash-pill dash-pill-yellow">Mar 15</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-dashboard.card>
</div>

@push('scripts')
<script>
(function() {
  var days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
  var sales = [420, 580, 390, 710, 950, 820, 680];
  var expenses = [180, 220, 150, 280, 310, 200, 160];
  var maxVal = Math.max.apply(null, sales.concat(expenses));
  var chart = document.getElementById('dashBarChart');
  var labels = document.getElementById('dashBarLabels');
  if (!chart || !labels) return;
  for (var i = 0; i < days.length; i++) {
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
    lbl.textContent = days[i];
    labels.appendChild(lbl);
  }
})();
</script>
@endpush
@endsection
