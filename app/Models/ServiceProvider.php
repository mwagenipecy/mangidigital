<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceProvider extends Model
{
    public const TYPE_INTERNATIONAL_TRANSPORT = 'international_transport';
    public const TYPE_LOCAL_TRANSPORT = 'local_transport';
    public const TYPE_CLEARANCE_FORWARDING = 'clearance_forwarding';

    protected $fillable = [
        'organization_id',
        'product_category_id',
        'name',
        'type',
        'contact_phone',
        'contact_email',
        'address',
        'notes',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function stockOrders(): HasMany
    {
        return $this->hasMany(StockOrder::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_INTERNATIONAL_TRANSPORT => 'International transport',
            self::TYPE_LOCAL_TRANSPORT => 'Local transport',
            self::TYPE_CLEARANCE_FORWARDING => 'Clearance & forwarding',
            default => $this->type,
        };
    }
}
