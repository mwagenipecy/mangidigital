<?php

namespace App\Models\Concerns;

/**
 * Customer-facing tracking code: "CG" + 24 hex chars (96-bit random).
 * Distinct from UUID-shaped logistics_flow_token so lookups are unambiguous.
 */
trait GeneratesPublicTrackingCode
{
    /** @throws \RuntimeException */
    public static function generateUniquePublicTrackingCode(): string
    {
        $attempts = 0;
        do {
            $code = 'CG'.strtoupper(bin2hex(random_bytes(12)));
            $exists = static::query()->where('public_tracking_code', $code)->exists();
            $attempts++;
        } while ($exists && $attempts < 48);

        if ($exists) {
            throw new \RuntimeException('Could not generate a unique public tracking code.');
        }

        return $code;
    }

    public function ensurePublicTrackingCode(): void
    {
        if (! empty($this->public_tracking_code)) {
            return;
        }
        $this->forceFill([
            'public_tracking_code' => static::generateUniquePublicTrackingCode(),
        ])->saveQuietly();
    }

    public static function formatPublicTrackingCodeForDisplay(?string $code): string
    {
        if ($code === null || $code === '') {
            return '';
        }
        $c = strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', $code));
        if (! str_starts_with($c, 'CG') || strlen($c) !== 26) {
            return $code;
        }
        $rest = substr($c, 2);
        if (strlen($rest) !== 24 || ! ctype_xdigit($rest)) {
            return $code;
        }

        return 'CG-'.implode('-', str_split($rest, 4));
    }

    public function getFormattedPublicTrackingCodeAttribute(): string
    {
        return static::formatPublicTrackingCodeForDisplay($this->public_tracking_code);
    }
}
