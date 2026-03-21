<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientInstallmentPayment extends Model
{
    protected $fillable = [
        'organization_id',
        'client_payment_plan_id',
        'client_id',
        'recorded_by_user_id',
        'amount',
        'payment_method',
        'payment_reference',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'date',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function paymentPlan(): BelongsTo
    {
        return $this->belongsTo(ClientPaymentPlan::class, 'client_payment_plan_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }
}
