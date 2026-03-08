<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOrderItemReceipt extends Model
{
    protected $fillable = [
        'stock_order_item_id',
        'store_id',
        'quantity_received',
        'received_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity_received' => 'decimal:2',
        ];
    }

    public function stockOrderItem(): BelongsTo
    {
        return $this->belongsTo(StockOrderItem::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function receivedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
