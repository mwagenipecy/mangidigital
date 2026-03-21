<?php

namespace App\Jobs;

use App\Mail\ClientPaymentReminderMail;
use App\Models\ClientPaymentPlan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendClientPaymentReminderEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $planId)
    {
    }

    public function handle(): void
    {
        $plan = ClientPaymentPlan::query()
            ->with(['client'])
            ->withSum('installments', 'amount')
            ->find($this->planId);

        if (! $plan || ! $plan->client?->email || $plan->status === 'closed') {
            return;
        }

        Mail::to($plan->client->email)->send(new ClientPaymentReminderMail($plan));
    }
}
