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

<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">{{ __('Profile') }}</h1>
        <p class="dash-page-subtitle">{{ __('Update your name and email address') }}</p>
    </div>
</div>

<div class="dash-card" style="max-width: 32rem;">
    <form wire:submit="updateProfileInformation">
        <div style="margin-bottom: 16px;">
            <label for="profile-name" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">{{ __('Name') }}</label>
            <input type="text" id="profile-name" wire:model="name" required autofocus autocomplete="name" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            @error('name') <p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p> @enderror
        </div>
        <div style="margin-bottom: 16px;">
            <label for="profile-email" style="display:block;font-size:.8rem;font-weight:600;color:var(--dash-ink);margin-bottom:6px;">{{ __('Email') }}</label>
            <input type="email" id="profile-email" wire:model="email" required autocomplete="email" style="width:100%;padding:10px 14px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
            @error('email') <p style="margin:4px 0 0;font-size:.8rem;color:var(--dash-danger);">{{ $message }}</p> @enderror
            @if ($this->hasUnverifiedEmail)
                <p style="margin-top:8px;font-size:.85rem;color:var(--dash-muted);">
                    {{ __('Your email address is unverified.') }}
                    <button type="button" wire:click.prevent="resendVerificationNotification" style="background:none;border:none;padding:0;color:var(--dash-brand);cursor:pointer;text-decoration:underline;font-size:inherit;">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p style="margin-top:6px;font-size:.85rem;font-weight:600;color:var(--dash-ok);">{{ __('A new verification link has been sent to your email address.') }}</p>
                @endif
            @endif
        </div>
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <button type="submit" class="dash-btn dash-btn-brand" data-test="update-profile-button">{{ __('Save') }}</button>
            <x-action-message on="profile-updated" style="font-size:.9rem;color:var(--dash-ok);">{{ __('Saved.') }}</x-action-message>
        </div>
    </form>
</div>

@if ($this->showDeleteUser)
    <div style="max-width: 32rem; margin-top: 24px;">
        <livewire:pages::settings.delete-user-form />
    </div>
@endif
