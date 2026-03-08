@php
    $user = auth()->user();
    $initials = $user ? collect(explode(' ', $user->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->join('') : 'U';
    $headerBizName = $user->organization?->name ?? ($user->isAdmin() ? 'Admin' : 'My Business');
@endphp
<header class="dash-header" id="dashboardHeader">
    <button type="button" class="dash-hdr-toggle" onclick="window.dashToggleSidebar()" aria-label="Toggle sidebar">
        <span id="dash-toggle-icon"><flux:icon.chevron-left class="size-5" /></span>
    </button>
    <div class="dash-hdr-breadcrumb">
        <span class="dash-hdr-bc-item">Mangi Digital</span>
        <span class="dash-hdr-bc-sep">›</span>
        <span class="dash-hdr-bc-item active" id="dash-breadcrumb-active">Dashboard</span>
    </div>
    <div class="dash-hdr-search">
        <span class="dash-hdr-search-icon"><flux:icon.magnifying-glass class="size-4" /></span>
        <input type="text" placeholder="Search orders, clients, expenses…">
        <kbd>⌘K</kbd>
    </div>
    <div class="dash-hdr-right">
        <button type="button" class="dash-hdr-icon-btn" title="Add new" onclick="window.dashToast('Quick add opened')">
            <flux:icon.plus class="size-5" />
        </button>
        <div style="position:relative">
            <button type="button" class="dash-hdr-icon-btn" onclick="window.dashToggleNotif()" id="dash-notif-btn" title="Notifications">
                <flux:icon.bell class="size-5" />
                <span class="dash-hdr-badge">3</span>
            </button>
            <x-dashboard.notification-dropdown />
        </div>
        <div class="dash-hdr-divider"></div>
        <div style="position:relative">
            <div class="dash-hdr-profile" onclick="window.dashToggleProfile()" id="dash-profile-btn">
                <div class="dash-hdr-profile-avatar">{{ $initials }}</div>
                <div class="dash-hdr-profile-info">
                    <div class="dash-hdr-profile-name">{{ $user->name ?? 'User' }}</div>
                    <div class="dash-hdr-profile-biz">{{ $headerBizName }}</div>
                </div>
                <span class="dash-hdr-profile-chevron"><flux:icon.chevron-down class="size-3" /></span>
            </div>
            <x-dashboard.profile-dropdown />
        </div>
    </div>
</header>
