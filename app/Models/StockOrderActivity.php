<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOrderActivity extends Model
{
    public const ACTION_CREATED = 'created';
    public const ACTION_STATUS_CHANGED = 'status_changed';
    public const ACTION_CHARGES_UPDATED = 'charges_updated';
    public const ACTION_RECEIVED_TO_INVENTORY = 'received_to_inventory';

    public $timestamps = false;

    protected $fillable = [
        'stock_order_id',
        'user_id',
        'action',
        'description',
        'changes',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function stockOrder(): BelongsTo
    {
        return $this->belongsTo(StockOrder::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
