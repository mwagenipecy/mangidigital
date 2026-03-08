@extends('layouts.account')

@section('title', __('Register'))

@section('panel-tag')
    <div class="account-panel-tag">✦ Step 1 of 4</div>
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
        <div class="account-f-item"><div class="account-f-dot">3</div>Choose your plan</div>
        <div class="account-f-item"><div class="account-f-dot">4</div>Secure payment</div>
    </div>
@endsection

@section('content')
<div class="account-form-wrap" x-data="registerForm()" x-init="init()">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('home') }}" class="account-logo account-logo-dark no-underline text-[1.1rem]" wire:navigate>
            <span class="account-logo-icon w-[30px] h-[30px] text-[.7rem]">MD</span>
            Mangi<span>Digital</span>
        </a>
        <a href="{{ route('login') }}" class="text-[.82rem] text-[var(--muted)]" wire:navigate>← Back to login</a>
    </div>

    {{-- Step bar --}}
    <div class="account-step-bar">
        <div class="account-step-bar-top">
            <div class="account-step-bar-label">Step <strong x-text="step"></strong> of <strong>4</strong></div>
            <div class="account-step-bar-label" style="color:var(--brand)" x-text="stepNames[step - 1]"></div>
        </div>
        <div class="account-step-dots">
            <div class="account-s-dot" :class="{ 'done': step > 1, 'active': step === 1, 'pending': step < 1 }" x-text="step > 1 ? '✓' : 1"></div>
            <div class="account-s-line" :class="{ 'done': step > 1 }"></div>
            <div class="account-s-dot" :class="{ 'done': step > 2, 'active': step === 2, 'pending': step < 2 }" x-text="step > 2 ? '✓' : 2"></div>
            <div class="account-s-line" :class="{ 'done': step > 2 }"></div>
            <div class="account-s-dot" :class="{ 'done': step > 3, 'active': step === 3, 'pending': step < 3 }" x-text="step > 3 ? '✓' : 3"></div>
            <div class="account-s-line" :class="{ 'done': step > 3 }"></div>
            <div class="account-s-dot" :class="{ 'done': step > 4, 'active': step === 4, 'pending': step < 4 }" x-text="step > 4 ? '✓' : 4"></div>
        </div>
        <div class="account-step-progress" style="margin-top:10px">
            <div class="account-step-progress-fill" :style="'width:' + (step/4*100) + '%'"></div>
        </div>
        <div class="account-step-names">
            <div class="account-s-name" :class="{ 'active': step === 1, 'done': step > 1 }">Personal</div>
            <div class="account-s-name" :class="{ 'active': step === 2, 'done': step > 2 }">Business</div>
            <div class="account-s-name" :class="{ 'active': step === 3, 'done': step > 3 }">Plan</div>
            <div class="account-s-name" :class="{ 'active': step === 4, 'done': step > 4 }">Payment</div>
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
                <div class="account-phone-group">
                    <input type="text" name="phone_code" value="+255" readonly class="account-phone-code">
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="712 345 678" class="account-phone-num {{ $errors->has('phone') ? 'border-red-500' : '' }}">
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
            <p class="account-form-sub">Select the plan that fits your business. You can upgrade anytime.</p>

            <div class="account-plan-grid">
                <label class="account-plan-card" :class="{ 'selected': selectedPlan === 'basic' }" @click="selectedPlan = 'basic'">
                    <input type="radio" name="plan" value="basic" :checked="selectedPlan === 'basic'">
                    <div class="account-plan-name">Basic</div>
                    <div class="account-plan-price">TZS 25K</div>
                    <div class="account-plan-period">/month</div>
                </label>
                <label class="account-plan-card relative" :class="{ 'selected': selectedPlan === 'pro' }" @click="selectedPlan = 'pro'">
                    <span class="account-plan-badge-pop">Popular</span>
                    <input type="radio" name="plan" value="pro" :checked="selectedPlan === 'pro'">
                    <div class="account-plan-name">Professional</div>
                    <div class="account-plan-price">TZS 65K</div>
                    <div class="account-plan-period">/month</div>
                </label>
                <label class="account-plan-card" :class="{ 'selected': selectedPlan === 'biz' }" @click="selectedPlan = 'biz'">
                    <input type="radio" name="plan" value="biz" :checked="selectedPlan === 'biz'">
                    <div class="account-plan-name">Business+</div>
                    <div class="account-plan-price">TZS 150K</div>
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

            <div class="account-field">
                <label for="billing">Billing Cycle</label>
                <div class="account-input-wrap">
                    <select id="billing" name="billing" x-model="billing" @change="updateSummary()">
                        <option value="monthly">Monthly</option>
                        <option value="annual">Annual (save 20%)</option>
                    </select>
                </div>
            </div>

            <div class="account-summary-box">
                <div class="account-summary-row"><span>Plan</span><span x-text="planData[selectedPlan].name"></span></div>
                <div class="account-summary-row"><span>Billing</span><span x-text="billing === 'annual' ? 'Annual' : 'Monthly'"></span></div>
                <div class="account-summary-row"><span>14-day free trial</span><span class="text-[var(--ok)]">Included</span></div>
                <div class="account-summary-row total"><span>Due Today</span><span class="val" x-text="summaryTotal"></span></div>
            </div>

            <div class="flex gap-3">
                <button type="button" class="account-btn account-btn-outline flex-1 py-3" @click="goStep(2)">← Back</button>
                <button type="button" class="account-btn account-btn-brand flex-[2] py-3" @click="goStep(4)">Continue to Payment →</button>
            </div>
        </div>

        {{-- Step 4: Payment --}}
        <div x-show="step === 4" x-cloak style="display: none;">
            <h2 class="account-form-title">Secure Payment</h2>
            <p class="account-form-sub">Your payment is protected with 256-bit SSL encryption</p>

            <div class="account-pay-methods">
                <div class="account-pay-method" :class="{ 'selected': payMethod === 'card' }" @click="payMethod = 'card'">
                    <span class="pm-icon"><flux:icon.credit-card class="size-5" /></span>Card
                </div>
                <div class="account-pay-method" :class="{ 'selected': payMethod === 'mpesa' }" @click="payMethod = 'mpesa'">
                    <span class="pm-icon"><flux:icon.device-phone-mobile class="size-5" /></span>M-Pesa
                </div>
                <div class="account-pay-method" :class="{ 'selected': payMethod === 'tigo' }" @click="payMethod = 'tigo'">
                    <span class="pm-icon"><flux:icon.device-phone-mobile class="size-5" /></span>Tigo Pesa
                </div>
                <div class="account-pay-method" :class="{ 'selected': payMethod === 'airtel' }" @click="payMethod = 'airtel'">
                    <span class="pm-icon"><flux:icon.globe-europe-africa class="size-5" /></span>Airtel Money
                </div>
            </div>

            <div x-show="payMethod === 'card'">
                <div class="account-card-visual">
                    <div class="account-card-chip"></div>
                    <div class="account-card-number" x-text="cardDisplay || '•••• •••• •••• ••••'"></div>
                    <div class="account-card-bottom">
                        <div><div class="account-card-label">Card Holder</div><div class="account-card-value" x-text="(cardName || 'YOUR NAME').toUpperCase()"></div></div>
                        <div><div class="account-card-label">Expires</div><div class="account-card-value" x-text="cardExp || 'MM/YY'"></div></div>
                    </div>
                </div>
                <div class="account-field">
                    <label>Card Number</label>
                    <div class="account-input-wrap"><input type="text" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" x-model="cardNumber" @input="formatCardInput($event)"></div>
                </div>
                <div class="account-field">
                    <label>Cardholder Name</label>
                    <div class="account-input-wrap"><input type="text" name="card_name" placeholder="As on card" x-model="cardName"></div>
                </div>
                <div class="account-row-2">
                    <div class="account-field">
                        <label>Expiry (MM/YY)</label>
                        <div class="account-input-wrap"><input type="text" name="card_exp" placeholder="MM/YY" maxlength="5" x-model="cardExp" @input="formatExpInput($event)"></div>
                    </div>
                    <div class="account-field">
                        <label>CVV</label>
                        <div class="account-input-wrap"><input type="text" name="card_cvv" placeholder="•••" maxlength="4"></div>
                    </div>
                </div>
            </div>

            <div x-show="payMethod !== 'card'" x-cloak>
                <div class="rounded-[var(--r-sm)] p-5 mb-5 text-center border border-[var(--border)] bg-[var(--brand-10)]">
                    <flux:icon.device-phone-mobile class="size-8 mx-auto mb-2 text-[var(--brand)]" />
                    <div class="font-bold text-[var(--ink)] mb-1" x-text="payMethod === 'mpesa' ? 'M-Pesa' : payMethod === 'tigo' ? 'Tigo Pesa' : 'Airtel Money'"></div>
                    <div class="text-[.85rem] text-[var(--muted)]">Enter your number to receive a payment prompt</div>
                </div>
                <div class="account-field">
                    <label>Mobile Number</label>
                    <div class="account-phone-group account-input-wrap">
                        <input type="text" value="+255" readonly class="account-phone-code" style="padding:12px 16px;">
                        <input type="tel" name="mobile_number" placeholder="712 345 678" class="account-phone-num">
                    </div>
                </div>
                <p class="text-[.82rem] text-[var(--muted)] bg-[var(--border-lt)] p-3 rounded-[var(--r-sm)] border border-[var(--border-lt)]">
                    You will receive a USSD prompt. Enter your PIN to confirm payment of <strong class="text-[var(--brand)]" x-text="summaryTotal"></strong>.
                </p>
            </div>

            <input type="hidden" name="payment_method" x-model="payMethod">
            <input type="hidden" name="plan" x-model="selectedPlan">
            <input type="hidden" name="billing" x-model="billing">

            <div class="account-summary-box">
                <div class="account-summary-row"><span>Plan</span><span x-text="planData[selectedPlan].name"></span></div>
                <div class="account-summary-row"><span>Free trial</span><span class="text-[var(--ok)]">14 days</span></div>
                <div class="account-summary-row total"><span>Total Charged</span><span class="val" x-text="summaryTotal"></span></div>
            </div>

            <div class="account-check-row">
                <input type="checkbox" id="save_payment" name="save_payment" value="1">
                <label for="save_payment" class="text-[.82rem]">Save payment method for future renewals</label>
            </div>

            <div class="flex gap-3">
                <button type="button" class="account-btn account-btn-outline flex-1 py-3" @click="goStep(3)">← Back</button>
                <button type="submit" class="account-btn account-btn-brand flex-[2] py-3">Pay & Submit Application →</button>
            </div>

            <div class="text-center mt-4 flex justify-center gap-3 flex-wrap text-[.73rem] text-[var(--muted)]">
                <span><flux:icon.lock-closed class="size-3.5 inline" /> SSL Secured</span>
                <span><flux:icon.credit-card class="size-3.5 inline" /> PCI Compliant</span>
                <span><flux:icon.shield-check class="size-3.5 inline" /> Fraud Protected</span>
            </div>
        </div>
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
        stepNames: ['Personal Details', 'Business Information', 'Choose Plan', 'Secure Payment'],
        pw: '',
        pwLabel: 'Enter a password',
        pwStrengthClass: '',
        pwScore: 0,
        logoFile: '',
        selectedPlan: 'pro',
        billing: 'monthly',
        payMethod: 'card',
        cardNumber: '',
        cardName: '',
        cardExp: '',
        planData: {
            basic: { name: 'Basic', price: 25000, features: ['Up to 50 clients', 'Order management', 'Basic expense tracking', 'Sales recording', 'Partial payment tracking'] },
            pro: { name: 'Professional', price: 65000, features: ['Up to 300 clients', 'Full expense tracking + categories', 'Sales analytics & charts', 'Instalment payment plans', 'SMS payment reminders', 'Up to 3 staff accounts'] },
            biz: { name: 'Business+', price: 150000, features: ['Unlimited clients', 'Everything in Professional', 'Inventory management', 'Full financial reports (PDF)', 'Unlimited staff accounts', 'API integrations', 'Priority 24/7 support', 'Custom branding'] }
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
        updateSummary() {},
        get summaryTotal() {
            const p = this.planData[this.selectedPlan];
            const price = this.billing === 'annual' ? Math.round(p.price * 12 * 0.8) : p.price;
            return 'TZS ' + price.toLocaleString();
        },
        get cardDisplay() {
            const v = (this.cardNumber || '').replace(/\D/g, '').substring(0, 16);
            return v ? v.replace(/(.{4})/g, '$1 ').trim() : '•••• •••• •••• ••••';
        },
        formatCardInput(e) {
            let v = e.target.value.replace(/\D/g, '').substring(0, 16);
            this.cardNumber = v.replace(/(.{4})/g, '$1 ').trim();
        },
        formatExpInput(e) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length > 2) v = v.substring(0, 2) + '/' + v.substring(2, 4);
            this.cardExp = v;
        },
        onSubmit(e) {
            if (this.step !== 4) { e.preventDefault(); return false; }
            return true;
        }
    };
}
</script>
@endpush
@endsection
