<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOrderItem extends Model
{
    protected $fillable = [
        'stock_order_id',
        'product_id',
        'quantity_ordered',
        'order_price_per_unit',
    ];

    protected function casts(): array
    {
        return [
            'quantity_ordered' => 'decimal:2',
            'order_price_per_unit' => 'decimal:2',
        ];
    }

    public function stockOrder(): BelongsTo
    {
        return $this->belongsTo(StockOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(StockOrderItemReceipt::class);
    }

    public function quantityReceived(): float
    {
        return (float) $this->receipts->sum('quantity_received');
    }

    public function quantityPending(): float
    {
        return max(0, (float) $this->quantity_ordered - $this->quantityReceived());
    }
}
