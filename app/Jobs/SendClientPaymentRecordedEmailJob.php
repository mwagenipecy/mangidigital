<?php

namespace App\Jobs;

use App\Mail\ClientPaymentRecordedMail;
use App\Models\ClientInstallmentPayment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendClientPaymentRecordedEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $paymentId)
    {
    }

    public function handle(): void
    {
        $payment = ClientInstallmentPayment::query()
            ->with(['client', 'paymentPlan'])
            ->find($this->paymentId);

        if (! $payment || ! $payment->client?->email) {
            return;
        }

        Mail::to($payment->client->email)->send(new ClientPaymentRecordedMail($payment));
    }
}
