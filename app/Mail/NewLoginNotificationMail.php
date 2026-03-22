<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewLoginNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $ipLocation  From IpLocationLookup::lookup()
     */
    public function __construct(
        public string $userName,
        public string $ipAddress,
        public string $userAgent,
        public string $loggedInAtFormatted,
        public array $ipLocation,
        public ?string $browserLatitude,
        public ?string $browserLongitude,
        public ?string $browserAccuracyMeters,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('New sign-in to :app', ['app' => config('app.name')]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-login-notification',
        );
    }
}
