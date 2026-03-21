<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Income;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    private const PLAN_PRICES = [
        'trial' => 0,
        'pro' => 9000,
        'biz' => 15000,
    ];

    /**
     * Validate and create a newly registered user with organization and subscription.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => $this->emailRules(null),
            'password' => $this->passwordRules(),
            'business_name' => ['nullable', 'string', 'max:255'],
            'plan' => ['nullable', 'string', 'in:trial,pro,biz'],
            'billing' => ['nullable', 'string', 'in:monthly,annual'],
            'payment_method' => ['nullable', 'string', 'max:50'],
        ];

        if (isset($input['name']) && ! isset($input['first_name'])) {
            $rules['name'] = $this->nameRules();
            unset($rules['first_name'], $rules['last_name']);
        }

        Validator::make($input, $rules)->validate();

        $name = isset($input['name'])
            ? $input['name']
            : trim(($input['first_name'] ?? '').' '.($input['last_name'] ?? ''));

        $businessName = $input['business_name'] ?? $name.'\'s Business';
        $plan = $input['plan'] ?? 'trial';
        $billing = $input['billing'] ?? 'monthly';
        $paymentMethod = $input['payment_method'] ?? 'free_trial';

        $price = self::PLAN_PRICES[$plan] ?? self::PLAN_PRICES['trial'];
        $amount = $billing === 'annual' ? (int) round($price * 12 * 0.8) : $price;

        $subscriptionStart = Carbon::today();
        $subscriptionEnd = $billing === 'annual'
            ? $subscriptionStart->copy()->addYear()
            : $subscriptionStart->copy()->addMonth();

        return DB::transaction(function () use ($input, $name, $businessName, $subscriptionStart, $subscriptionEnd, $plan, $amount, $paymentMethod) {
            $organization = Organization::create([
                'name' => $businessName,
                'address' => $input['business_address'] ?? null,
                'subscription_start' => $subscriptionStart,
                'subscription_end' => $subscriptionEnd,
                'status' => 'active',
            ]);

            $user = User::create([
                'name' => $name,
                'email' => $input['email'],
                'password' => $input['password'],
                'organization_id' => $organization->id,
                'status' => User::STATUS_APPROVED,
            ]);

            Payment::create([
                'organization_id' => $organization->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'currency' => 'TZS',
                'payment_method' => $paymentMethod,
                'reference' => 'Registration',
                'recorded_at' => now(),
            ]);

            Income::create([
                'organization_id' => $organization->id,
                'amount' => $amount,
                'currency' => 'TZS',
                'period' => $subscriptionStart->format('Y-m'),
                'recorded_at' => now(),
            ]);

            return $user;
        });
    }
}
