@extends('layouts.account')

@section('title', __('Register'))

@section('panel-tag')
    <div class="account-panel-tag">✦ Step 1 of 3</div>
@endsection

@section('panel-title')
    <h2 class="account-panel-title">Create Your<br><em>Business Account</em></h2>
@endsection

@section('panel-desc')
    <p class="account-panel-desc">Join 2,400+ Tanzanian businesses managing smarter with Mangi Digital. It takes less than 5 minutes to set up.</p>
@endsection

@section('panel-features')
    <div class="account-feature-list">
        <div class="account-f-item"><div class="account-f-dot">1</div>Personal details</div>
        <div class="account-f-item"><div class="account-f-dot">2</div>Business information</div>
        <div class="account-f-item"><div class="account-f-dot">3</div>Choose free trial and continue</div>
        <div class="account-f-item"><div class="account-f-dot">✓</div>Instant dashboard access</div>
    </div>
@endsection

@section('content')
<div class="account-form-wrap" x-data="registerForm()" x-init="init()">
    <div class="account-form-header mb-6 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('home') }}" class="account-logo account-logo-dark no-underline text-[1.1rem]" wire:navigate>
            <span class="account-logo-icon w-[30px] h-[30px] text-[.7rem]">MD</span>
            Mangi<span>Digital</span>
        </a>
        <a href="{{ route('login') }}" class="text-[.82rem] text-[var(--muted)]" wire:navigate>← Back to login</a>
    </div>

    {{-- Step bar --}}
    <div class="account-step-bar">
        <div class="account-step-bar-top">
            <div class="account-step-bar-label">Step <strong x-text="step"></strong> of <strong>3</strong></div>
            <div class="account-step-bar-label" style="color:var(--brand)" x-text="stepNames[step - 1]"></div>
        </div>
        <div class="account-step-dots">
            <div class="account-s-dot" :class="{ 'done': step > 1, 'active': step === 1, 'pending': step < 1 }" x-text="step > 1 ? '✓' : 1"></div>
            <div class="account-s-line" :class="{ 'done': step > 1 }"></div>
            <div class="account-s-dot" :class="{ 'done': step > 2, 'active': step === 2, 'pending': step < 2 }" x-text="step > 2 ? '✓' : 2"></div>
            <div class="account-s-line" :class="{ 'done': step > 2 }"></div>
            <div class="account-s-dot" :class="{ 'done': step > 3, 'active': step === 3, 'pending': step < 3 }" x-text="step > 3 ? '✓' : 3"></div>
        </div>
        <div class="account-step-progress" style="margin-top:10px">
            <div class="account-step-progress-fill" :style="'width:' + (step/3*100) + '%'"></div>
        </div>
        <div class="account-step-names">
            <div class="account-s-name" :class="{ 'active': step === 1, 'done': step > 1 }">Personal</div>
            <div class="account-s-name" :class="{ 'active': step === 2, 'done': step > 2 }">Business</div>
            <div class="account-s-name" :class="{ 'active': step === 3, 'done': step > 3 }">Plan</div>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-5" @submit="onSubmit">
        @csrf

        {{-- Step 1: Personal --}}
        <div x-show="step === 1" x-cloak>
            <h2 class="account-form-title">Personal Details</h2>
            <p class="account-form-sub">Tell us about the account owner</p>

            <div class="account-row-2">
                <div class="account-field">
                    <label for="first_name">First Name <span class="req">*</span></label>
                    <div class="account-input-wrap">
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="Amina" required
                            class="{{ $errors->has('first_name') ? 'border-red-500' : '' }}">
                    </div>
                    @error('first_name')<div class="account-field-error">{{ $message }}</div>@enderror
                </div>
                <div class="account-field">
                    <label for="last_name">Last Name <span class="req">*</span></label>
                    <div class="account-input-wrap">
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Mkwawa" required
                            class="{{ $errors->has('last_name') ? 'border-red-500' : '' }}">
                    </div>
                    @error('last_name')<div class="account-field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="account-field">
                <label for="email">Email Address <span class="req">*</span></label>
                <div class="account-input-wrap has-icon">
                    <span class="account-input-icon"><flux:icon.envelope class="size-4 text-[var(--muted)]" /></span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@yourbusiness.com" required
                        class="{{ $errors->has('email') ? 'border-red-500' : '' }}">
                </div>
                @error('email')<div class="account-field-error">{{ $message }}</div>@enderror
            </div>

            <div class="account-field">
                <label for="phone">Phone Number <span class="req">*</span></label>
                <div class="account-input-wrap">
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="0712 345 678" required
                        class="{{ $errors->has('phone') ? 'border-red-500' : '' }}">
                </div>
                @error('phone')<div class="account-field-error">{{ $message }}</div>@enderror
            </div>

            <div class="account-field">
                <label for="password">Password <span class="req">*</span></label>
                <div class="account-input-wrap has-icon" x-data="{ showPw: false }">
                    <span class="account-input-icon"><flux:icon.lock-closed class="size-4 text-[var(--muted)]" /></span>
                    <input :type="showPw ? 'text' : 'password'" id="password" name="password" placeholder="Create a strong password" required
                        x-model="pw" @input="checkPwStrength($event.target.value)"
                        class="{{ $errors->has('password') ? 'border-red-500' : '' }}">
                    <button type="button" class="account-input-eye" @click="showPw = !showPw" tabindex="-1">
                        <flux:icon.eye x-show="!showPw" class="size-4" />
                        <flux:icon.eye-slash x-show="showPw" class="size-4" x-cloak />
                    </button>
                </div>
                <div class="account-pw-strength">
                    <div class="account-pw-bars">
                        <div class="account-pw-bar" :class="pwScore >= 1 ? pwStrengthClass : ''"></div>
                        <div class="account-pw-bar" :class="pwScore >= 2 ? pwStrengthClass : ''"></div>
                        <div class="account-pw-bar" :class="pwScore >= 3 ? pwStrengthClass : ''"></div>
                        <div class="account-pw-bar" :class="pwScore >= 4 ? pwStrengthClass : ''"></div>
                    </div>
                    <div class="account-pw-label" x-text="pwLabel"></div>
                </div>
                @error('password')<div class="account-field-error">{{ $message }}</div>@enderror
            </div>

            <div class="account-field">
                <label for="password_confirmation">Confirm Password <span class="req">*</span></label>
                <div class="account-input-wrap has-icon">
                    <span class="account-input-icon"><flux:icon.lock-closed class="size-4 text-[var(--muted)]" /></span>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat your password" required>
                </div>
                @error('password_confirmation')<div class="account-field-error">{{ $message }}</div>@enderror
            </div>

            <div class="account-check-row">
                <input type="checkbox" id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
                <label for="terms">I agree to the <a href="{{ route('terms') }}" class="text-[var(--brand)]" wire:navigate>Terms of Service</a> and <a href="{{ route('privacy') }}" class="text-[var(--brand)]" wire:navigate>Privacy Policy</a> of Mangi Digital</label>
            </div>

            <button type="button" class="account-btn account-btn-brand account-btn-full" @click="goStep(2)">Continue to Business Info →</button>
        </div>

        {{-- Step 2: Business --}}
        <div x-show="step === 2" x-cloak style="display: none;">
            <h2 class="account-form-title">Business Information</h2>
            <p class="account-form-sub">Tell us about your business</p>

            <div class="account-field">
                <label for="business_name">Business Name <span class="req">*</span></label>
                <div class="account-input-wrap has-icon">
                    <span class="account-input-icon"><flux:icon.cube class="size-4 text-[var(--muted)]" /></span>
                    <input type="text" id="business_name" name="business_name" value="{{ old('business_name') }}" placeholder="e.g. Amina Fashion Store">
                </div>
                @error('business_name')<div class="account-field-error">{{ $message }}</div>@enderror
            </div>

            <div class="account-row-2">
                <div class="account-field">
                    <label for="business_type">Business Type <span class="req">*</span></label>
                    <div class="account-input-wrap">
                        <select id="business_type" name="business_type">
                            <option value="">Select type</option>
                            <option value="Retail Shop" {{ old('business_type') == 'Retail Shop' ? 'selected' : '' }}>Retail Shop</option>
                            <option value="Wholesale" {{ old('business_type') == 'Wholesale' ? 'selected' : '' }}>Wholesale</option>
                            <option value="Restaurant / Food" {{ old('business_type') == 'Restaurant / Food' ? 'selected' : '' }}>Restaurant / Food</option>
                            <option value="Electronics" {{ old('business_type') == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                            <option value="Fashion & Clothing" {{ old('business_type') == 'Fashion & Clothing' ? 'selected' : '' }}>Fashion & Clothing</option>
                            <option value="Pharmacy" {{ old('business_type') == 'Pharmacy' ? 'selected' : '' }}>Pharmacy</option>
                            <option value="Salon & Beauty" {{ old('business_type') == 'Salon & Beauty' ? 'selected' : '' }}>Salon & Beauty</option>
                            <option value="Hardware & Building" {{ old('business_type') == 'Hardware & Building' ? 'selected' : '' }}>Hardware & Building</option>
                            <option value="Other" {{ old('business_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
                <div class="account-field">
                    <label for="region">Region <span class="req">*</span></label>
                    <div class="account-input-wrap">
                        <select id="region" name="region">
                            <option value="">Select region</option>
                            <option value="Dar es Salaam" {{ old('region') == 'Dar es Salaam' ? 'selected' : '' }}>Dar es Salaam</option>
                            <option value="Arusha" {{ old('region') == 'Arusha' ? 'selected' : '' }}>Arusha</option>
                            <option value="Mwanza" {{ old('region') == 'Mwanza' ? 'selected' : '' }}>Mwanza</option>
                            <option value="Dodoma" {{ old('region') == 'Dodoma' ? 'selected' : '' }}>Dodoma</option>
                            <option value="Mbeya" {{ old('region') == 'Mbeya' ? 'selected' : '' }}>Mbeya</option>
                            <option value="Morogoro" {{ old('region') == 'Morogoro' ? 'selected' : '' }}>Morogoro</option>
                            <option value="Tanga" {{ old('region') == 'Tanga' ? 'selected' : '' }}>Tanga</option>
                            <option value="Zanzibar" {{ old('region') == 'Zanzibar' ? 'selected' : '' }}>Zanzibar</option>
                            <option value="Other" {{ old('region') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="account-field">
                <label for="business_address">Business Address</label>
                <div class="account-input-wrap">
                    <input type="text" id="business_address" name="business_address" value="{{ old('business_address') }}" placeholder="Street, Ward, District">
                </div>
            </div>

            <div class="account-field">
                <label for="tin">TIN Number <span class="text-[.75rem] text-[var(--muted)] font-normal">(optional — for invoicing)</span></label>
                <div class="account-input-wrap">
                    <input type="text" id="tin" name="tin" value="{{ old('tin') }}" placeholder="e.g. 123-456-789">
                </div>
            </div>

            <div class="account-field">
                <label class="text-[.75rem] text-[var(--muted)] font-normal">Business Logo (optional)</label>
                <div class="account-upload-box" @click="$refs.logoUpload.click()">
                    <div class="account-upload-icon"><flux:icon.squares-2x2 class="size-8 text-[var(--muted)]" /></div>
                    <div class="account-upload-title">Click to upload logo</div>
                    <div class="account-upload-sub">PNG, JPG up to 2MB</div>
                    <input type="file" x-ref="logoUpload" name="logo" accept="image/*" class="hidden" @change="logoFile = $event.target.files[0]?.name || ''">
                </div>
                <div class="account-file-preview" :class="{ 'show': logoFile }" x-show="logoFile" x-cloak style="display: none;">
                    <flux:icon.squares-2x2 class="size-5 text-[var(--muted)]" />
                    <span class="account-file-name" x-text="logoFile"></span>
                    <button type="button" class="account-file-remove" @click="logoFile = ''; if ($refs.logoUpload) $refs.logoUpload.value = ''"><flux:icon.x-mark class="size-4" /></button>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" class="account-btn account-btn-outline flex-1 py-3" @click="goStep(1)">← Back</button>
                <button type="button" class="account-btn account-btn-brand flex-[2] py-3" @click="goStep(3)">Continue to Plan →</button>
            </div>
        </div>

        {{-- Step 3: Plan --}}
        <div x-show="step === 3" x-cloak style="display: none;">
            <h2 class="account-form-title">Choose Your Plan</h2>
            <p class="account-form-sub">Free Trial is active now. Paid plans are listed but not available yet.</p>

            <div class="account-plan-grid">
                <label class="account-plan-card" :class="{ 'selected': selectedPlan === 'trial' }" @click="selectedPlan = 'trial'">
                    <input type="radio" name="plan" value="trial" :checked="selectedPlan === 'trial'">
                    <div class="account-plan-name">Free Trial</div>
                    <div class="account-plan-price">TZS 0</div>
                    <div class="account-plan-period">/month</div>
                </label>
                <label class="account-plan-card relative opacity-60 cursor-not-allowed" :class="{ 'selected': selectedPlan === 'pro' }">
                    <span class="account-plan-badge-pop">Coming Soon</span>
                    <input type="radio" name="plan" value="pro" :checked="selectedPlan === 'pro'" disabled>
                    <div class="account-plan-name">Professional</div>
                    <div class="account-plan-price">TZS 9K</div>
                    <div class="account-plan-period">/month</div>
                </label>
                <label class="account-plan-card opacity-60 cursor-not-allowed" :class="{ 'selected': selectedPlan === 'biz' }">
                    <input type="radio" name="plan" value="biz" :checked="selectedPlan === 'biz'" disabled>
                    <div class="account-plan-name">Business+</div>
                    <div class="account-plan-price">TZS 15K</div>
                    <div class="account-plan-period">/month</div>
                </label>
            </div>

            <div class="account-summary-box" id="plan-features-box">
                <div class="text-[.78rem] font-bold text-[var(--ink)] mb-2 uppercase tracking-wide" x-text="planData[selectedPlan].name + ' Plan Includes'"></div>
                <div class="flex flex-col gap-1.5 text-[.83rem] text-[var(--text)]">
                    <template x-for="f in planData[selectedPlan].features" :key="f">
                        <div>✓ <span x-text="f"></span></div>
                    </template>
                </div>
            </div>

            <input type="hidden" name="billing" value="monthly">

            <div class="account-summary-box">
                <div class="account-summary-row"><span>Plan</span><span x-text="planData[selectedPlan].name"></span></div>
                <div class="account-summary-row"><span>Billing</span><span>Monthly</span></div>
                <div class="account-summary-row"><span>14-day free trial</span><span class="text-[var(--ok)]">Included</span></div>
                <div class="account-summary-row total"><span>Due Today</span><span class="val" x-text="summaryTotal"></span></div>
            </div>

            <div class="flex gap-3">
                <button type="button" class="account-btn account-btn-outline flex-1 py-3" @click="goStep(2)">← Back</button>
                <button type="submit" class="account-btn account-btn-brand flex-[2] py-3">Create Free Trial Account →</button>
            </div>
        </div>
        <input type="hidden" name="payment_method" value="free_trial">
        <input type="hidden" name="plan" x-model="selectedPlan">
    </form>

    <p class="text-center text-[.8rem] text-[var(--muted)] mt-6">
        Already have an account?
        <a href="{{ route('login') }}" class="text-[var(--brand)]" wire:navigate>Sign in →</a>
    </p>
</div>

@push('account-scripts')
<script>
function registerForm() {
    return {
        step: 1,
        stepNames: ['Personal Details', 'Business Information', 'Choose Plan'],
        pw: '',
        pwLabel: 'Enter a password',
        pwStrengthClass: '',
        pwScore: 0,
        logoFile: '',
        selectedPlan: 'trial',
        billing: 'monthly',
        planData: {
            trial: { name: 'Free Trial', price: 0, features: ['Up to 50 clients', 'Order management', 'Expense tracking', 'Sales recording', 'Partial payment tracking', 'Instant account approval'] },
            pro: { name: 'Professional', price: 9000, features: ['Up to 300 clients', 'Full expense tracking + categories', 'Sales analytics & charts', 'Instalment payment plans', 'SMS payment reminders', 'Up to 3 staff accounts'] },
            biz: { name: 'Business+', price: 15000, features: ['Unlimited clients', 'Everything in Professional', 'Inventory management', 'Full financial reports (PDF)', 'Unlimited staff accounts', 'API integrations', 'Priority 24/7 support', 'Custom branding'] }
        },
        init() {
            this.step = 1;
        },
        goStep(n) {
            if (n === 2) {
                const f = document.querySelector('form');
                if (!document.getElementById('first_name').value?.trim() || !document.getElementById('last_name').value?.trim() ||
                    !document.getElementById('email').value?.trim() || !document.getElementById('password').value ||
                    document.getElementById('password').value.length < 8 || document.getElementById('password').value !== document.getElementById('password_confirmation').value ||
                    !document.getElementById('terms').checked) {
                    f.reportValidity();
                    return;
                }
            }
            this.step = n;
            window.scrollTo(0, 0);
        },
        checkPwStrength(val) {
            if (!val) { this.pwLabel = 'Enter a password'; this.pwStrengthClass = ''; this.pwScore = 0; return; }
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            this.pwScore = score;
            this.pwStrengthClass = score <= 1 ? 'weak' : score === 2 ? 'fair' : 'good';
            this.pwLabel = ['', 'Weak', 'Fair', 'Good', 'Strong'][score];
        },
        get summaryTotal() {
            const p = this.planData[this.selectedPlan];
            const price = p.price;
            return 'TZS ' + price.toLocaleString();
        },
        onSubmit(e) {
            if (this.step !== 3) { e.preventDefault(); return false; }
            return true;
        }
    };
}
</script>
@endpush
@endsection
