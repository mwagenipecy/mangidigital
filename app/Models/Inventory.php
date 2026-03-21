<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Inventory extends Model
{
    protected $fillable = [
        'organization_id',
        'uuid',
        'product_id',
        'store_id',
        'quantity',
        'price_per_unit',
        'is_out_of_stock',
    ];

    protected static function booted(): void
    {
        static::creating(function (Inventory $inventory): void {
            if (empty($inventory->uuid)) {
                $inventory->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Public URLs use UUID so sequential IDs are not exposed.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'price_per_unit' => 'decimal:2',
            'is_out_of_stock' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Oldest first — matches ledger / running balance (see inventory show).
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class)
            ->orderBy('created_at')
            ->orderBy('id');
    }

    public function getDisplayPriceAttribute(): string
    {
        return $this->price_per_unit !== null
            ? (string) $this->price_per_unit
            : (string) ($this->product->price ?? 0);
    }
}
