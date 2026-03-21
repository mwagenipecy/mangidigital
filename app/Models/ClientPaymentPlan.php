<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientPaymentPlan extends Model
{
    protected $fillable = [
        'organization_id',
        'client_id',
        'plan_name',
        'goal_amount',
        'status',
        'started_at',
        'closed_at',
        'last_reminded_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'goal_amount' => 'decimal:2',
            'started_at' => 'date',
            'closed_at' => 'datetime',
            'last_reminded_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(ClientInstallmentPayment::class);
    }
}
