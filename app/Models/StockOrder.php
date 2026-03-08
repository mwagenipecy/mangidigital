<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOrder extends Model
{
    public const ORDER_TYPE_INTERNATIONAL = 'international';
    public const ORDER_TYPE_LOCAL = 'local';

    public const STATUS_ORDERED = 'ordered';
    public const STATUS_IN_TRANSIT = 'in_transit';
    public const STATUS_RECEIVED = 'received';

    protected $fillable = [
        'organization_id',
        'order_type',
        'service_provider_id',
        'amount_paid',
        'transport_charges',
        'other_charges',
        'payment_date',
        'estimated_receive_date',
        'received_at',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'transport_charges' => 'decimal:2',
            'other_charges' => 'decimal:2',
            'payment_date' => 'date',
            'estimated_receive_date' => 'date',
            'received_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function serviceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOrderItem::class, 'stock_order_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(StockOrderActivity::class)->orderByDesc('created_at');
    }

    public function getOrderTypeLabelAttribute(): string
    {
        return $this->order_type === self::ORDER_TYPE_INTERNATIONAL ? 'International' : 'Local';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ORDERED => 'Ordered',
            self::STATUS_IN_TRANSIT => 'In transit',
            self::STATUS_RECEIVED => 'Received',
            default => $this->status,
        };
    }

    public function totalOrderValue(): float
    {
        return (float) $this->items->sum(fn (StockOrderItem $item) => $item->quantity_ordered * $item->order_price_per_unit);
    }

    public function totalCharges(): float
    {
        return (float) $this->amount_paid + (float) $this->transport_charges + (float) $this->other_charges;
    }
}
