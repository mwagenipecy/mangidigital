@props(['title', 'subtitle' => null, 'actionLabel' => null, 'actionUrl' => '#'])
<div class="dash-card">
    <div class="dash-card-header">
        <div>
            <div class="dash-card-title">{{ $title }}</div>
            @if($subtitle)
                <div class="dash-card-subtitle">{{ $subtitle }}</div>
            @endif
        </div>
        @if($actionLabel)
            <a href="{{ $actionUrl }}" class="dash-card-action">{{ $actionLabel }}</a>
        @endif
    </div>
    {{ $slot }}
</div>
