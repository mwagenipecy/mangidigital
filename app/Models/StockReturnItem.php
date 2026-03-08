<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReturnItem extends Model
{
    protected $fillable = [
        'stock_return_id',
        'product_id',
        'product_name_override',
        'store_id',
        'quantity',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
        ];
    }

    public function stockReturn(): BelongsTo
    {
        return $this->belongsTo(StockReturn::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function getDisplayProductNameAttribute(): string
    {
        return $this->product_name_override ?? $this->product?->name ?? '—';
    }
}
