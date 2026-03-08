@php
    $user = auth()->user();
    $initials = $user ? collect(explode(' ', $user->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->join('') : 'U';
    $mobileBizName = $user->organization?->name ?? ($user->isAdmin() ? 'Admin' : 'My Business');
    $mobilePlan = 'Professional Plan';
@endphp
<div class="dash-mobile-sidebar" id="dashMobileSidebar">
    <div style="padding:16px;border-bottom:1px solid var(--dash-border);display:flex;align-items:center;justify-content:space-between">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2" wire:navigate>
            <div class="dash-sb-logo-icon">MD</div>
            <span style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:900;color:var(--dash-ink)">Mangi<span style="color:var(--dash-brand)">Digital</span></span>
        </a>
        <button type="button" onclick="window.dashCloseMobileSidebar()" style="color:var(--dash-muted);font-size:1.2rem;padding:4px;border:none;background:none;cursor:pointer">
            <flux:icon.x-mark class="size-5" />
        </button>
    </div>
    <div style="padding:10px 8px;flex:1;overflow-y:auto">
        <div class="dash-sb-group-label">Main</div>
        <a href="{{ route('dashboard') }}" class="dash-sb-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.home class="size-5" /></span>
            <span class="dash-sb-label">Dashboard</span>
        </a>
        <a href="{{ route('sales.index') }}" class="dash-sb-item {{ request()->routeIs('sales.*') ? 'active' : '' }}" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.shopping-cart class="size-5" /></span>
            <span class="dash-sb-label">Sales</span>
        </a>
        <a href="{{ route('expenses.index') }}" class="dash-sb-item {{ request()->routeIs('expenses.*') || request()->routeIs('expense-categories.*') ? 'active' : '' }}" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.banknotes class="size-5" /></span>
            <span class="dash-sb-label">Expenses</span>
        </a>
        <div class="dash-sb-group-label">Clients</div>
        <a href="{{ route('clients.index') }}" class="dash-sb-item {{ request()->routeIs('clients.*') ? 'active' : '' }}" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.users class="size-5" /></span>
            <span class="dash-sb-label">Clients</span>
        </a>
        <a href="#" class="dash-sb-item" onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.credit-card class="size-5" /></span>
            <span class="dash-sb-label">Payments</span>
            <span class="dash-sb-badge warn">5</span>
        </a>
        <a href="{{ route('invoices.index') }}" class="dash-sb-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.document-text class="size-5" /></span>
            <span class="dash-sb-label">Invoices</span>
        </a>
        <div class="dash-sb-group-label">Organization</div>
        <a href="{{ route('stores.index') }}" class="dash-sb-item {{ request()->routeIs('stores.*') ? 'active' : '' }}" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.cube class="size-5" /></span>
            <span class="dash-sb-label">Register store/shop</span>
        </a>
        <div class="dash-sb-group-label">Product</div>
        <a href="{{ route('product-categories.index') }}" class="dash-sb-item {{ request()->routeIs('product-categories.*') ? 'active' : '' }}" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.squares-2x2 class="size-5" /></span>
            <span class="dash-sb-label">Product category</span>
        </a>
        <a href="{{ route('products.index') }}" class="dash-sb-item {{ request()->routeIs('products.*') ? 'active' : '' }}" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.cube class="size-5" /></span>
            <span class="dash-sb-label">Product</span>
        </a>
        <div class="dash-sb-group-label">Business</div>
        <a href="{{ route('service-providers.index') }}" class="dash-sb-item {{ request()->routeIs('service-providers.*') ? 'active' : '' }}" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.truck class="size-5" /></span>
            <span class="dash-sb-label">Clearance & transport</span>
        </a>
        <a href="{{ route('stock-orders.index') }}" class="dash-sb-item {{ request()->routeIs('stock-orders.*') ? 'active' : '' }}" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.shopping-bag class="size-5" /></span>
            <span class="dash-sb-label">Order stock</span>
        </a>
        <a href="{{ route('inventory.index') }}" class="dash-sb-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.archive-box class="size-5" /></span>
            <span class="dash-sb-label">Inventory</span>
        </a>
        <a href="{{ route('logistics.index') }}" class="dash-sb-item {{ request()->routeIs('logistics.*') ? 'active' : '' }}" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.truck class="size-5" /></span>
            <span class="dash-sb-label">Logistics</span>
        </a>
        <a href="{{ route('stock-returns.index') }}" class="dash-sb-item {{ request()->routeIs('stock-returns.*') ? 'active' : '' }}" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.arrow-uturn-left class="size-5" /></span>
            <span class="dash-sb-label">Return stocks</span>
        </a>
        <a href="#" class="dash-sb-item" onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.presentation-chart-bar class="size-5" /></span>
            <span class="dash-sb-label">Reports</span>
        </a>
        @if($user->isAdmin() ?? false)
        <div class="dash-sb-group-label">Administration</div>
        <a href="{{ route('admin.index') }}" class="dash-sb-item {{ request()->routeIs('admin.*') ? 'active' : '' }}" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.shield-check class="size-5" /></span>
            <span class="dash-sb-label">Administration</span>
        </a>
        @endif
        <div class="dash-sb-group-label">Account</div>
        <a href="{{ route('profile.edit') }}" class="dash-sb-item" wire:navigate onclick="window.dashCloseMobileSidebar()">
            <span class="dash-sb-icon"><flux:icon.cog class="size-5" /></span>
            <span class="dash-sb-label">Settings</span>
        </a>
    </div>
    <div style="padding:10px 8px 16px;border-top:1px solid var(--dash-border)">
        <div style="display:flex;align-items:center;gap:10px;padding:10px;border-radius:var(--dash-r-sm);background:var(--dash-brand-10);border:1px solid var(--dash-border-dk)">
            <div class="dash-sb-avatar">{{ $initials }}</div>
            <div>
                <div style="font-size:.83rem;font-weight:700;color:var(--dash-ink)">{{ $user->name ?? 'User' }}</div>
                <div style="font-size:.72rem;color:var(--dash-brand)">{{ $mobileBizName }} · {{ $mobilePlan }}</div>
            </div>
        </div>
    </div>
</div>
