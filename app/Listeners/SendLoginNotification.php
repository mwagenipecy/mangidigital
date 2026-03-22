<?php

namespace App\Listeners;

use App\Jobs\SendLoginNotificationJob;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class SendLoginNotification
{
    public function __construct(
        protected Request $request
    ) {}

    public function handle(Login $event): void
    {
        if (! config('login-notification.enabled', true)) {
            return;
        }

        $user = $event->user;
        if (! $user instanceof User || ! filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        SendLoginNotificationJob::dispatch(
            userId: $user->id,
            ipAddress: (string) $this->request->ip(),
            userAgent: (string) ($this->request->userAgent() ?? ''),
            browserLatitude: $this->normalizeLatitude($this->request->input('login_geo_lat')),
            browserLongitude: $this->normalizeLongitude($this->request->input('login_geo_lng')),
            browserAccuracyMeters: $this->normalizeOptionalAccuracy($this->request->input('login_geo_accuracy')),
            loggedInAtIso: now()->toIso8601String(),
        );
    }

    private function normalizeLatitude(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (! is_numeric($value)) {
            return null;
        }
        $f = (float) $value;
        if ($f < -90 || $f > 90) {
            return null;
        }

        return (string) round($f, 6);
    }

    private function normalizeLongitude(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (! is_numeric($value)) {
            return null;
        }
        $f = (float) $value;
        if ($f < -180 || $f > 180) {
            return null;
        }

        return (string) round($f, 6);
    }

    private function normalizeOptionalAccuracy(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (! is_numeric($value)) {
            return null;
        }
        $n = (int) round((float) $value);
        if ($n < 0 || $n > 50_000) {
            return null;
        }

        return (string) $n;
    }
}
