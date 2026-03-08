@php $user = auth()->user(); $initials = $user ? collect(explode(' ', $user->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->join('') : 'U'; @endphp
<div class="dash-mobile-header" id="dashMobileHeader">
    <button type="button" class="dash-mob-icon-btn" onclick="window.dashOpenMobileSidebar()" aria-label="Menu">
        <flux:icon.bars-2 class="size-5" />
    </button>
    <a href="{{ route('dashboard') }}" class="dash-mob-logo">
        <div class="dash-mob-logo-icon">MD</div>
        <div class="dash-mob-logo-text">Mangi<span>Digital</span></div>
    </a>
    <div class="dash-mob-right">
        <button type="button" class="dash-mob-icon-btn" onclick="window.dashToggleMobSearch()" aria-label="Search">
            <flux:icon.magnifying-glass class="size-5" />
        </button>
        <button type="button" class="dash-mob-icon-btn" onclick="window.dashToggleNotif()" aria-label="Notifications">
            <flux:icon.bell class="size-5" />
            <span class="dash-mob-badge">3</span>
        </button>
        <button type="button" class="dash-mob-icon-btn" onclick="window.dashToggleProfile()" aria-label="Profile">
            <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--dash-brand),#1d6e80);display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;color:#fff;font-family:'Space Mono',monospace;border:2px solid var(--dash-brand-15)">{{ $initials }}</div>
        </button>
    </div>
</div>
<div class="dash-mob-search-wrap" id="dashMobSearchWrap">
    <div class="dash-mob-search-inner">
        <span class="dash-hdr-search-icon"><flux:icon.magnifying-glass class="size-4" /></span>
        <input type="text" placeholder="Search orders, clients, expenses…" autofocus>
    </div>
</div>
