<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sale extends Model
{
    public const DELIVERY_STATUS_PENDING = 'pending';
    public const DELIVERY_STATUS_IN_TRANSIT = 'in_transit';
    public const DELIVERY_STATUS_ARRIVED = 'arrived';
    public const DELIVERY_STATUS_RECEIVED = 'received';

    protected $fillable = [
        'organization_id',
        'client_id',
        'client_name',
        'client_phone',
        'sale_date',
        'subtotal',
        'delivery_requested',
        'delivery_cost',
        'delivery_service_provider_id',
        'delivery_status',
        'delivery_dispatched_at',
        'delivery_arrived_at',
        'delivery_received_at',
        'delivery_pickup_office',
        'total',
        'receipt_number',
        'notes',
        'created_by',
    ];

    protected static function booted(): void
    {
        static::saving(function (Sale $sale): void {
            if ($sale->delivery_requested && empty($sale->logistics_flow_token)) {
                $sale->logistics_flow_token = (string) Str::uuid();
            }
        });
    }

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'subtotal' => 'decimal:2',
            'delivery_requested' => 'boolean',
            'delivery_cost' => 'decimal:2',
            'total' => 'decimal:2',
            'delivery_dispatched_at' => 'datetime',
            'delivery_arrived_at' => 'datetime',
            'delivery_received_at' => 'datetime',
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

    public function deliveryServiceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'delivery_service_provider_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockReturns(): HasMany
    {
        return $this->hasMany(StockReturn::class);
    }

    public function getDisplayClientNameAttribute(): string
    {
        return $this->client_name ?? $this->client?->name ?? '—';
    }

    public function getDisplayClientPhoneAttribute(): string
    {
        return $this->client_phone ?? $this->client?->phone ?? '—';
    }

    public function getDeliveryStatusLabelAttribute(): string
    {
        return match ($this->delivery_status) {
            self::DELIVERY_STATUS_PENDING => 'Pending',
            self::DELIVERY_STATUS_IN_TRANSIT => 'In transit',
            self::DELIVERY_STATUS_ARRIVED => 'Arrived',
            self::DELIVERY_STATUS_RECEIVED => 'Received by customer',
            default => $this->delivery_status ?? 'Pending',
        };
    }

    /** @return array<string, string> [value => label] of allowed next statuses (forward only) */
    public function allowedNextDeliveryStatuses(): array
    {
        $current = $this->delivery_status ?? self::DELIVERY_STATUS_PENDING;
        $nexts = match ($current) {
            self::DELIVERY_STATUS_PENDING => [self::DELIVERY_STATUS_IN_TRANSIT, self::DELIVERY_STATUS_ARRIVED, self::DELIVERY_STATUS_RECEIVED],
            self::DELIVERY_STATUS_IN_TRANSIT => [self::DELIVERY_STATUS_ARRIVED, self::DELIVERY_STATUS_RECEIVED],
            self::DELIVERY_STATUS_ARRIVED => [self::DELIVERY_STATUS_RECEIVED],
            self::DELIVERY_STATUS_RECEIVED => [],
            default => [self::DELIVERY_STATUS_IN_TRANSIT, self::DELIVERY_STATUS_ARRIVED, self::DELIVERY_STATUS_RECEIVED],
        };
        $labels = [
            self::DELIVERY_STATUS_IN_TRANSIT => 'In transit',
            self::DELIVERY_STATUS_ARRIVED => 'Arrived',
            self::DELIVERY_STATUS_RECEIVED => 'Received by customer',
        ];
        $out = [];
        foreach ($nexts as $v) {
            $out[$v] = $labels[$v] ?? $v;
        }
        return $out;
    }
}
