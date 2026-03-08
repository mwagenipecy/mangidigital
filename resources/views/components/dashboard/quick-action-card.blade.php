@props(['href' => '#', 'label', 'sub'])
<a href="{{ $href }}" class="dash-qa-card">
    <div class="dash-qa-icon">{{ $icon ?? '' }}</div>
    <div class="dash-qa-label">{{ $label }}</div>
    @if($sub ?? null)
        <div class="dash-qa-sub">{{ $sub }}</div>
    @endif
</a>
