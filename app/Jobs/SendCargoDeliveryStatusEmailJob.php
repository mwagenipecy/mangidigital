<?php

namespace App\Jobs;

use App\Mail\CargoDeliveryStatusUpdatedMail;
use App\Models\CargoShipment;
use App\Models\Sale;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendCargoDeliveryStatusEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ?int $saleId = null,
        public ?int $cargoShipmentId = null,
    ) {
    }

    public function handle(): void
    {
        if ($this->saleId !== null) {
            $this->sendForSale($this->saleId);

            return;
        }
        if ($this->cargoShipmentId !== null) {
            $this->sendForCargoShipment($this->cargoShipmentId);
        }
    }

    private function sendForSale(int $saleId): void
    {
        $sale = Sale::query()
            ->with(['client', 'organization'])
            ->find($saleId);

        if (! $sale || ! $sale->delivery_requested || ! $sale->logistics_flow_token) {
            return;
        }

        $email = $sale->client?->email;
        if (empty($email)) {
            return;
        }

        Mail::to($email)->send(CargoDeliveryStatusUpdatedMail::fromSale($sale));
    }

    private function sendForCargoShipment(int $cargoShipmentId): void
    {
        $cargo = CargoShipment::query()
            ->with(['organization'])
            ->find($cargoShipmentId);

        if (! $cargo || ! $cargo->logistics_flow_token) {
            return;
        }

        $email = $cargo->client_email;
        if (empty($email)) {
            return;
        }

        Mail::to($email)->send(CargoDeliveryStatusUpdatedMail::fromCargoShipment($cargo));
    }
}
