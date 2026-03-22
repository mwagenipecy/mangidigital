<?php

namespace App\Mail;

use App\Models\CargoShipment;
use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CargoDeliveryStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $clientName,
        public string $organizationName,
        public string $referenceDisplay,
        public string $statusLabel,
        public string $trackUrl,
        /** Public tracking number (CG-…); customers enter this at /track-cargo */
        public string $trackingCode,
        /** Form URL where customers can paste the tracking code */
        public string $trackFormUrl,
        public ?string $pickupOffice,
        public ?string $dispatchedAtFormatted,
        public ?string $arrivedAtFormatted,
        public ?string $receivedAtFormatted,
        public string $summaryLine = '',
    ) {}

    public static function fromSale(Sale $sale): self
    {
        $sale->loadMissing(['organization', 'items.product']);
        $sale->ensurePublicTrackingCode();
        $sale->refresh();

        $summary = $sale->items->map(fn ($i) => $i->display_product_name.' × '.number_format($i->quantity, 0))->join(', ');

        return new self(
            clientName: $sale->display_client_name,
            organizationName: $sale->organization?->name ?? config('app.name'),
            referenceDisplay: $sale->receipt_number ?? '#'.$sale->id,
            statusLabel: $sale->delivery_status_label,
            trackUrl: route('cargo.track', ['flow_token' => $sale->public_tracking_code], true),
            trackingCode: Sale::formatPublicTrackingCodeForDisplay($sale->public_tracking_code),
            trackFormUrl: route('cargo.track.form', [], true),
            pickupOffice: $sale->delivery_pickup_office,
            dispatchedAtFormatted: $sale->delivery_dispatched_at?->timezone(config('app.timezone'))->format('d M Y H:i'),
            arrivedAtFormatted: $sale->delivery_arrived_at?->timezone(config('app.timezone'))->format('d M Y H:i'),
            receivedAtFormatted: $sale->delivery_received_at?->timezone(config('app.timezone'))->format('d M Y H:i'),
            summaryLine: $summary ?: '',
        );
    }

    public static function fromCargoShipment(CargoShipment $cargo): self
    {
        $cargo->loadMissing(['organization']);
        $cargo->ensurePublicTrackingCode();
        $cargo->refresh();

        return new self(
            clientName: $cargo->client_name,
            organizationName: $cargo->organization?->name ?? config('app.name'),
            referenceDisplay: $cargo->reference_number,
            statusLabel: $cargo->delivery_status_label,
            trackUrl: route('cargo.track', ['flow_token' => $cargo->public_tracking_code], true),
            trackingCode: CargoShipment::formatPublicTrackingCodeForDisplay($cargo->public_tracking_code),
            trackFormUrl: route('cargo.track.form', [], true),
            pickupOffice: $cargo->delivery_pickup_office,
            dispatchedAtFormatted: $cargo->delivery_dispatched_at?->timezone(config('app.timezone'))->format('d M Y H:i'),
            arrivedAtFormatted: $cargo->delivery_arrived_at?->timezone(config('app.timezone'))->format('d M Y H:i'),
            receivedAtFormatted: $cargo->delivery_received_at?->timezone(config('app.timezone'))->format('d M Y H:i'),
            summaryLine: $cargo->cargo_description ?? '',
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('[:org] Cargo update: :status', [
                'org' => $this->organizationName,
                'status' => $this->statusLabel,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cargo-delivery-status-updated',
            with: [
                'clientName' => $this->clientName,
                'organizationName' => $this->organizationName,
                'referenceDisplay' => $this->referenceDisplay,
                'statusLabel' => $this->statusLabel,
                'trackUrl' => $this->trackUrl,
                'trackingCode' => $this->trackingCode,
                'trackFormUrl' => $this->trackFormUrl,
                'pickupOffice' => $this->pickupOffice,
                'dispatchedAtFormatted' => $this->dispatchedAtFormatted,
                'arrivedAtFormatted' => $this->arrivedAtFormatted,
                'receivedAtFormatted' => $this->receivedAtFormatted,
                'summaryLine' => $this->summaryLine,
            ],
        );
    }
}
