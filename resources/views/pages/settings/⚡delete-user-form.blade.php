<?php

use Livewire\Component;

new class extends Component {}; ?>

<div>
    <div class="dash-card dash-form-card" style="margin-top: 24px; border-color: rgba(239,68,68,.2); background: rgba(239,68,68,.04);">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title" style="color: var(--dash-danger);">{{ __('Delete account') }}</div>
                <div class="dash-card-subtitle">{{ __('Once deleted, your account and all its data (clients, sales, invoices, etc.) cannot be recovered.') }}</div>
            </div>
        </div>
        <div class="dash-form-section">
            <p class="dash-form-hint" style="margin-bottom: 16px;">{{ __('If you are sure you want to permanently delete your account, click the button below. You will be asked to enter your password to confirm.') }}</p>
            <div class="dash-form-actions">
                <flux:modal.trigger name="confirm-user-deletion">
                    <button type="button" class="dash-btn dash-btn-danger" data-test="delete-user-button">
                        {{ __('Delete account') }}
                    </button>
                </flux:modal.trigger>
            </div>
        </div>
    </div>

    <livewire:pages::settings.delete-user-modal />
</div>
