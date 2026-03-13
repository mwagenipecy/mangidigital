@props(['id' => 'dash-profile-dropdown'])
@php $user = auth()->user(); @endphp
<div class="dash-dd" id="{{ $id }}">
    <div class="dash-dd-header">
        <div class="dash-dd-name">{{ $user->name ?? 'User' }}</div>
        <div class="dash-dd-email">{{ $user->email ?? '' }}</div>
    </div>
    <a href="{{ route('profile.edit') }}" class="dash-dd-item" wire:navigate><span class="dash-dd-item-icon"><flux:icon.user class="size-4" /></span>My Profile</a>
    <a href="{{ route('settings.business') }}" class="dash-dd-item" wire:navigate><span class="dash-dd-item-icon"><flux:icon.cube class="size-4" /></span>Business Settings</a>
    <a href="{{ route('settings.billing') }}" class="dash-dd-item" wire:navigate><span class="dash-dd-item-icon"><flux:icon.credit-card class="size-4" /></span>Billing & Plan</a>
    <a href="{{ route('settings.notifications') }}" class="dash-dd-item" wire:navigate><span class="dash-dd-item-icon"><flux:icon.bell class="size-4" /></span>Notification Preferences</a>
    <div class="dash-dd-divider"></div>
    <a href="{{ route('terms') }}" class="dash-dd-item" wire:navigate><span class="dash-dd-item-icon"><flux:icon.question-mark-circle class="size-4" /></span>Help & Support</a>
    <a href="{{ route('profile.edit') }}" class="dash-dd-item" wire:navigate><span class="dash-dd-item-icon"><flux:icon.cog class="size-4" /></span>Settings</a>
    <div class="dash-dd-divider"></div>
    <form method="POST" action="{{ route('logout') }}" class="contents">
        @csrf
        <button type="submit" class="dash-dd-item danger w-full"><span class="dash-dd-item-icon"><flux:icon.arrow-right-start-on-rectangle class="size-4" /></span>Sign Out</button>
    </form>
</div>
