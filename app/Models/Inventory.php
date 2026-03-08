<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $fillable = [
        'organization_id',
        'product_id',
        'store_id',
        'quantity',
        'price_per_unit',
        'is_out_of_stock',
    ];

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

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class)->orderByDesc('created_at');
    }

    public function getDisplayPriceAttribute(): string
    {
        return $this->price_per_unit !== null
            ? (string) $this->price_per_unit
            : (string) ($this->product->price ?? 0);
    }
}
