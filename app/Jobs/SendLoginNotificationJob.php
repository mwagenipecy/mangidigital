<?php

namespace App\Jobs;

use App\Mail\NewLoginNotificationMail;
use App\Models\User;
use App\Services\IpLocationLookup;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendLoginNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(
        public int $userId,
        public string $ipAddress,
        public string $userAgent,
        public ?string $browserLatitude,
        public ?string $browserLongitude,
        public ?string $browserAccuracyMeters,
        public string $loggedInAtIso,
    ) {}

    public function handle(): void
    {
        if (! config('login-notification.enabled', true)) {
            return;
        }

        $user = User::query()->find($this->userId);
        if (! $user || ! $user->email) {
            return;
        }

        $ipLocation = IpLocationLookup::lookup($this->ipAddress);

        $loggedInAt = \Carbon\Carbon::parse($this->loggedInAtIso)
            ->timezone(config('app.timezone'))
            ->format('d M Y H:i T');

        // Queue the mailable (implements ShouldQueue) — actual send runs in a separate queue job.
        Mail::to($user->email)->queue(new NewLoginNotificationMail(
            userName: $user->name,
            ipAddress: $this->ipAddress,
            userAgent: $this->userAgent,
            loggedInAtFormatted: $loggedInAt,
            ipLocation: $ipLocation,
            browserLatitude: $this->browserLatitude,
            browserLongitude: $this->browserLongitude,
            browserAccuracyMeters: $this->browserAccuracyMeters,
        ));
    }
}
