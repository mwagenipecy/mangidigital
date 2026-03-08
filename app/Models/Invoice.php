<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'organization_id',
        'client_id',
        'invoice_number',
        'origin',
        'destination',
        'issue_date',
        'due_date',
        'status',
        'paid_at',
        'subtotal',
        'total',
        'notes',
        'created_by',
        'issuer_name',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
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

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort');
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function getDisplayNumberAttribute(): string
    {
        return $this->invoice_number ?? 'INV-' . $this->id;
    }

    public function getDestinationNameAttribute(): string
    {
        if ($this->client) {
            $parts = array_filter([$this->client->name, $this->client->address, $this->client->phone, $this->client->email]);
            return implode("\n", $parts);
        }
        return $this->destination ?? '—';
    }
}
