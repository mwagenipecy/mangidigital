<?php

use App\Concerns\PasswordValidationRules;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    use PasswordValidationRules;

    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
    <form method="POST" wire:submit="deleteUser" style="padding: 0 4px;">
        <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--dash-ink); margin-bottom: 6px; font-family: 'Playfair Display', serif;">{{ __('Are you sure you want to delete your account?') }}</h2>
        <p style="font-size: .9rem; color: var(--dash-muted); line-height: 1.5; margin-bottom: 20px;">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Enter your password below to confirm.') }}</p>

        <div class="dash-form-field" style="margin-bottom: 20px;">
            <label for="delete-user-password">{{ __('Password') }}</label>
            <input type="password" id="delete-user-password" wire:model="password" required autocomplete="current-password" style="width: 100%; padding: 10px 14px; border: 1.5px solid var(--dash-border); border-radius: var(--dash-r-sm); font-size: .9rem; font-family: 'DM Sans', sans-serif;">
            @error('password')<p class="dash-form-error">{{ $message }}</p>@enderror
        </div>

        <div class="dash-form-actions" style="margin-top: 24px; justify-content: flex-end; gap: 10px;">
            <flux:modal.close>
                <button type="button" class="dash-btn dash-btn-outline">{{ __('Cancel') }}</button>
            </flux:modal.close>
            <button type="submit" class="dash-btn dash-btn-danger" data-test="confirm-delete-user-button">{{ __('Delete account') }}</button>
        </div>
    </form>
</flux:modal>
