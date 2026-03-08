@props([
    'value',
    'label',
    'trend' => null,
    'trendUp' => true,
    'color' => 'var(--dash-brand)',
    'bg' => 'var(--dash-brand-10)',
])
<div class="dash-kpi-card" style="--kpi-color:{{ $color }};--kpi-bg:{{ $bg }}">
    <div class="dash-kpi-top">
        <div class="dash-kpi-icon">{{ $icon ?? '' }}</div>
        @if($trend !== null)
            <div class="dash-kpi-trend {{ $trendUp ? 'up' : 'down' }}">{{ $trend }}</div>
        @endif
    </div>
    <div class="dash-kpi-value">{{ $value }}</div>
    <div class="dash-kpi-label">{{ $label }}</div>
</div>
