<nav class="dash-bottom-nav" id="dashBottomNav" role="navigation" aria-label="Main">
    <div class="dash-bn-inner">
        <a href="{{ route('dashboard') }}" class="dash-bn-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" wire:navigate aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
            <div class="dash-bn-icon"><flux:icon.home class="size-5" /></div>
            <span class="dash-bn-label">Home</span>
        </a>
        <a href="{{ route('sales.index') }}" class="dash-bn-item {{ request()->routeIs('sales.*') ? 'active' : '' }}" wire:navigate aria-label="Sales / Orders">
            <div class="dash-bn-icon">
                <flux:icon.shopping-cart class="size-5" />
            </div>
            <span class="dash-bn-label">Orders</span>
        </a>
        <a href="{{ route('sales.create') }}" class="dash-bn-item fab-item" wire:navigate aria-label="New sale">
            <div class="dash-bn-fab"><flux:icon.plus class="size-5" /></div>
            <span class="dash-bn-label">Add</span>
        </a>
        <a href="{{ route('clients.index') }}" class="dash-bn-item {{ request()->routeIs('clients.*') ? 'active' : '' }}" wire:navigate aria-label="Clients">
            <div class="dash-bn-icon"><flux:icon.users class="size-5" /></div>
            <span class="dash-bn-label">Clients</span>
        </a>
        <button type="button" class="dash-bn-item" onclick="window.dashOpenMobileSidebar()" aria-label="More menu">
            <div class="dash-bn-icon"><flux:icon.squares-2x2 class="size-5" /></div>
            <span class="dash-bn-label">More</span>
        </button>
    </div>
</nav>
