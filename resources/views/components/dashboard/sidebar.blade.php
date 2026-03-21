@props(['userName' => null, 'userRole' => null])
@php
    $user = auth()->user();
    $org = $user->organization ?? null;
    $businessName = $org?->name ?? ($user->isAdmin() ? 'Admin' : 'My Business');
    $twoLetters = $org ? collect(explode(' ', trim($org->name)))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->join('') : '';
    $businessInitials = $org
        ? strtoupper(strlen($twoLetters) >= 2 ? $twoLetters : mb_substr($org->name, 0, 2))
        : ($user->isAdmin() ? 'AD' : 'MB');
    $plan = 'Professional Plan';
    $userName = $userName ?? $user->name ?? 'User';
    $initials = $user ? collect(explode(' ', $user->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->join('') : 'U';
    $userRole = $userRole ?? 'Owner · Professional';
@endphp
<aside class="dash-sidebar" id="dashboardSidebar">
    <div class="dash-sb-brand">
        <div class="dash-sb-logo-icon">MD</div>
        <a href="{{ route('dashboard') }}" class="dash-sb-brand-text">Mangi<span>Digital</span></a>
    </div>
    <div class="dash-sb-biz">
        <div class="dash-sb-biz-avatar">{{ $businessInitials }}</div>
        <div class="dash-sb-biz-info">
            <div class="dash-sb-biz-name">{{ $businessName }}</div>
            <div class="dash-sb-biz-plan">✦ {{ $plan }}</div>
        </div>
    </div>
    <nav class="dash-sb-nav">
        <div class="dash-sb-group">
            <div class="dash-sb-group-label">Main</div>
            <a href="{{ route('dashboard') }}" class="dash-sb-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="dash-sb-icon"><flux:icon.home class="size-5" /></span>
                <span class="dash-sb-label">Dashboard</span>
            </a>
            <a href="{{ route('sales.index') }}" class="dash-sb-item {{ request()->routeIs('sales.*') ? 'active' : '' }}" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.shopping-cart class="size-5" /></span>
                <span class="dash-sb-label">Sales</span>
            </a>
            <a href="{{ route('expenses.index') }}" class="dash-sb-item {{ request()->routeIs('expenses.*') || request()->routeIs('expense-categories.*') ? 'active' : '' }}" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.banknotes class="size-5" /></span>
                <span class="dash-sb-label">Expenses</span>
            </a>
        </div>
        <div class="dash-sb-group">
            <div class="dash-sb-group-label">Clients</div>
            <a href="{{ route('clients.index') }}" class="dash-sb-item {{ request()->routeIs('clients.*') ? 'active' : '' }}" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.users class="size-5" /></span>
                <span class="dash-sb-label">Clients</span>
            </a>
            <a href="{{ route('payments.index') }}" class="dash-sb-item {{ request()->routeIs('payments.*') ? 'active' : '' }}" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.credit-card class="size-5" /></span>
                <span class="dash-sb-label">Payments</span>
            </a>
            <a href="{{ route('invoices.index') }}" class="dash-sb-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.document-text class="size-5" /></span>
                <span class="dash-sb-label">Invoices</span>
            </a>
        </div>
        <div class="dash-sb-group">
            <div class="dash-sb-group-label">Organization</div>
            <a href="{{ route('stores.index') }}" class="dash-sb-item {{ request()->routeIs('stores.*') ? 'active' : '' }}" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.cube class="size-5" /></span>
                <span class="dash-sb-label">Register store/shop</span>
            </a>
        </div>
        <div class="dash-sb-group">
            <div class="dash-sb-group-label">Product</div>
            <a href="{{ route('product-categories.index') }}" class="dash-sb-item {{ request()->routeIs('product-categories.*') ? 'active' : '' }}" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.squares-2x2 class="size-5" /></span>
                <span class="dash-sb-label">Product category</span>
            </a>
            <a href="{{ route('products.index') }}" class="dash-sb-item {{ request()->routeIs('products.*') ? 'active' : '' }}" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.cube class="size-5" /></span>
                <span class="dash-sb-label">Product</span>
            </a>
        </div>
        <div class="dash-sb-group">
            <div class="dash-sb-group-label">Business</div>
            <a href="{{ route('service-providers.index') }}" class="dash-sb-item {{ request()->routeIs('service-providers.*') ? 'active' : '' }}" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.truck class="size-5" /></span>
                <span class="dash-sb-label">Clearance & transport</span>
            </a>
            <a href="{{ route('stock-orders.index') }}" class="dash-sb-item {{ request()->routeIs('stock-orders.*') ? 'active' : '' }}" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.shopping-bag class="size-5" /></span>
                <span class="dash-sb-label">Order stock</span>
            </a>
            <a href="{{ route('inventory.index') }}" class="dash-sb-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.archive-box class="size-5" /></span>
                <span class="dash-sb-label">Inventory</span>
            </a>
            <a href="{{ route('logistics.index') }}" class="dash-sb-item {{ request()->routeIs('logistics.*') ? 'active' : '' }}" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.truck class="size-5" /></span>
                <span class="dash-sb-label">Logistics</span>
            </a>
            <a href="{{ route('stock-returns.index') }}" class="dash-sb-item {{ request()->routeIs('stock-returns.*') ? 'active' : '' }}" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.arrow-uturn-left class="size-5" /></span>
                <span class="dash-sb-label">Return stocks</span>
            </a>
            <a href="#" class="dash-sb-item">
                <span class="dash-sb-icon"><flux:icon.presentation-chart-bar class="size-5" /></span>
                <span class="dash-sb-label">Reports</span>
            </a>
            <a href="#" class="dash-sb-item">
                <span class="dash-sb-icon"><flux:icon.user class="size-5" /></span>
                <span class="dash-sb-label">Staff</span>
            </a>
        </div>
        @if($user->isAdmin() ?? false)
        <div class="dash-sb-group">
            <div class="dash-sb-group-label">Administration</div>
            <a href="{{ route('admin.index') }}" class="dash-sb-item {{ request()->routeIs('admin.*') ? 'active' : '' }}" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.shield-check class="size-5" /></span>
                <span class="dash-sb-label">Administration</span>
            </a>
        </div>
        @endif
        <div class="dash-sb-group">
            <div class="dash-sb-group-label">Account</div>
            <a href="{{ route('profile.edit') }}" class="dash-sb-item" wire:navigate>
                <span class="dash-sb-icon"><flux:icon.cog class="size-5" /></span>
                <span class="dash-sb-label">Settings</span>
            </a>
            <a href="#" class="dash-sb-item">
                <span class="dash-sb-icon"><flux:icon.question-mark-circle class="size-5" /></span>
                <span class="dash-sb-label">Help & Support</span>
            </a>
        </div>
    </nav>
    <div class="dash-sb-footer">
        <a href="#" class="dash-sb-footer-profile" x-data @click.prevent="$dispatch('toggle-profile')">
            <div class="dash-sb-avatar">{{ $initials }}</div>
            <div class="dash-sb-profile-info">
                <div class="dash-sb-profile-name">{{ $userName }}</div>
                <div class="dash-sb-profile-role">{{ $userRole }}</div>
            </div>
        </a>
    </div>
</aside>
