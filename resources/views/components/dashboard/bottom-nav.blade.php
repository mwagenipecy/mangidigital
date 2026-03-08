<nav class="dash-bottom-nav" id="dashBottomNav" role="navigation" aria-label="Main">
    <div class="dash-bn-inner">
        <a href="{{ route('dashboard') }}" class="dash-bn-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" wire:navigate aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
            <div class="dash-bn-icon"><flux:icon.home class="size-5" /></div>
            <span class="dash-bn-label">Home</span>
        </a>
        <a href="#" class="dash-bn-item" aria-label="Orders (12 new)">
            <div class="dash-bn-icon">
                <flux:icon.shopping-cart class="size-5" />
                <span class="dash-bn-notif-dot">12</span>
            </div>
            <span class="dash-bn-label">Orders</span>
        </a>
        <button type="button" class="dash-bn-item fab-item" onclick="window.dashToast('Quick add opened')" aria-label="Add new">
            <div class="dash-bn-fab"><flux:icon.plus class="size-5" /></div>
            <span class="dash-bn-label">Add</span>
        </button>
        <a href="#" class="dash-bn-item" aria-label="Clients">
            <div class="dash-bn-icon"><flux:icon.users class="size-5" /></div>
            <span class="dash-bn-label">Clients</span>
        </a>
        <a href="#" class="dash-bn-item" aria-label="More">
            <div class="dash-bn-icon"><flux:icon.squares-2x2 class="size-5" /></div>
            <span class="dash-bn-label">More</span>
        </a>
    </div>
</nav>
