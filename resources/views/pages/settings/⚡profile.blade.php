<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.dashboard-livewire', ['title' => 'Profile settings'])] #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<div>
    <div class="dash-page-header">
        <div>
            <h1 class="dash-page-title">{{ __('Profile') }}</h1>
            <p class="dash-page-subtitle">{{ __('Update your name and email address') }}</p>
        </div>
    </div>

    <div class="dash-card dash-form-card" style="max-width: 100%;">
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ __('Profile information') }}</div>
                <div class="dash-card-subtitle">{{ __('Name and email shown in the app') }}</div>
            </div>
        </div>
        <form wire:submit="updateProfileInformation" class="dash-form-section">
            <div class="dash-form-grid dash-form-grid--2" style="max-width:100%;">
                <div class="dash-form-field">
                    <label for="profile-name">{{ __('Name') }}</label>
                    <input type="text" id="profile-name" wire:model="name" required autofocus autocomplete="name">
                    @error('name') <p class="dash-form-error">{{ $message }}</p> @enderror
                </div>
                <div class="dash-form-field">
                    <label for="profile-email">{{ __('Email') }}</label>
                    <input type="email" id="profile-email" wire:model="email" required autocomplete="email">
                    @error('email') <p class="dash-form-error">{{ $message }}</p> @enderror
                </div>
            </div>
            @if ($this->hasUnverifiedEmail)
                <p class="dash-form-hint" style="margin-top: 4px;">
                    {{ __('Your email address is unverified.') }}
                    <button type="button" wire:click.prevent="resendVerificationNotification" style="background:none;border:none;padding:0;color:var(--dash-brand);cursor:pointer;text-decoration:underline;font-size:inherit;">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p style="margin-top:6px;font-size:.85rem;font-weight:600;color:var(--dash-ok);">{{ __('A new verification link has been sent to your email address.') }}</p>
                @endif
            @endif
            <div class="dash-form-actions">
                <button type="submit" class="dash-btn dash-btn-brand" data-test="update-profile-button">
                    <flux:icon.check class="size-4" />
                    {{ __('Save') }}
                </button>
                <x-action-message on="profile-updated" style="font-size:.9rem;color:var(--dash-ok);">{{ __('Saved.') }}</x-action-message>
            </div>
        </form>
    </div>

    @if ($this->showDeleteUser)
        <div style="max-width: 100%; margin-top: 24px;">
            <livewire:pages::settings.delete-user-form />
        </div>
    @endif
</div>
