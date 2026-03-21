<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CargoShipment extends Model
{
    public const DELIVERY_STATUS_PENDING = 'pending';

    public const DELIVERY_STATUS_IN_TRANSIT = 'in_transit';

    public const DELIVERY_STATUS_ARRIVED = 'arrived';

    public const DELIVERY_STATUS_RECEIVED = 'received';

    protected $fillable = [
        'organization_id',
        'logistics_flow_token',
        'reference_number',
        'client_name',
        'client_phone',
        'client_email',
        'cargo_description',
        'delivery_service_provider_id',
        'delivery_status',
        'delivery_pickup_office',
        'delivery_dispatched_at',
        'delivery_arrived_at',
        'delivery_received_at',
        'delivery_cost',
        'notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (CargoShipment $shipment): void {
            if (empty($shipment->logistics_flow_token)) {
                $shipment->logistics_flow_token = (string) Str::uuid();
            }
            if (empty($shipment->reference_number) && $shipment->organization_id) {
                $shipment->reference_number = static::nextReferenceForOrganization((int) $shipment->organization_id);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'delivery_cost' => 'decimal:2',
            'delivery_dispatched_at' => 'datetime',
            'delivery_arrived_at' => 'datetime',
            'delivery_received_at' => 'datetime',
        ];
    }

    public static function nextReferenceForOrganization(int $organizationId): string
    {
        $year = date('Y');
        $prefix = 'CGO-' . $year . '-';
        $last = static::query()
            ->where('organization_id', $organizationId)
            ->where('reference_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('reference_number');
        $num = 1;
        if ($last && preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $last, $m)) {
            $num = (int) $m[1] + 1;
        }

        return $prefix . str_pad((string) $num, 5, '0', STR_PAD_LEFT);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function deliveryServiceProvider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'delivery_service_provider_id');
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

    /** @return array<string, string> */
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
