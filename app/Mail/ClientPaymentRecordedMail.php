<?php

namespace App\Mail;

use App\Models\ClientInstallmentPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class ClientPaymentRecordedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ClientInstallmentPayment $payment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Received - '.$this->payment->paymentPlan?->plan_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-payment-recorded',
            with: [
                'paidTotal' => $this->paidTotal(),
                'goalAmount' => $this->goalAmount(),
                'remainingBalance' => $this->remainingBalance(),
                'verificationUrl' => $this->verificationUrl(),
            ],
        );
    }

    public function attachments(): array
    {
        $payment = $this->payment->loadMissing(['client', 'paymentPlan']);
        $clientName = str_replace(' ', '-', strtolower($payment->client?->name ?? 'client'));
        $filename = sprintf('payment-receipt-%s-%d.pdf', $clientName, $payment->id);

        return [
            Attachment::fromData(function (): string {
                $payment = $this->payment->loadMissing(['client', 'paymentPlan']);
                $pdf = Pdf::loadView('emails.client-payment-receipt-pdf', [
                    'payment' => $payment,
                    'paidTotal' => $this->paidTotal(),
                    'goalAmount' => $this->goalAmount(),
                    'remainingBalance' => $this->remainingBalance(),
                    'verificationUrl' => $this->verificationUrl(),
                    'qrSvg' => $this->qrSvg(),
                ]);

                return $pdf->output();
            }, $filename)->withMime('application/pdf'),
        ];
    }

    private function paidTotal(): float
    {
        $plan = $this->payment->paymentPlan;
        if (! $plan) {
            return (float) $this->payment->amount;
        }

        return (float) $plan->installments()->sum('amount');
    }

    private function goalAmount(): float
    {
        return (float) ($this->payment->paymentPlan?->goal_amount ?? 0);
    }

    private function remainingBalance(): float
    {
        return max(0, $this->goalAmount() - $this->paidTotal());
    }

    private function verificationUrl(): string
    {
        return URL::signedRoute('payments.receipts.verify', ['payment' => $this->payment->id]);
    }

    private function qrSvg(): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(150),
            new SvgImageBackEnd(),
        );

        return (new Writer($renderer))->writeString($this->verificationUrl());
    }
}
