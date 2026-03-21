<?php

namespace App\Mail;

use App\Models\ClientPaymentPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientPaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ClientPaymentPlan $plan)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Reminder - '.$this->plan->plan_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-payment-reminder',
        );
    }
}
