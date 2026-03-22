<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IpLocationLookup
{
    /**
     * Resolve a short human-readable location from a public IP.
     * Returns null fields on failure; private/local IPs skip the HTTP call.
     *
     * @return array{summary: string|null, city: string|null, region: string|null, country: string|null, isp: string|null, latitude: float|string|null, longitude: float|string|null}
     */
    public static function lookup(?string $ip): array
    {
        $empty = [
            'summary' => null,
            'city' => null,
            'region' => null,
            'country' => null,
            'isp' => null,
            'latitude' => null,
            'longitude' => null,
        ];

        if ($ip === null || $ip === '') {
            return $empty;
        }

        if (self::isPrivateOrLocalIp($ip)) {
            return array_merge($empty, [
                'summary' => __('Local or private network (not geolocated)'),
            ]);
        }

        try {
            $response = Http::timeout(3)
                ->acceptJson()
                ->get('https://ipwho.is/'.rawurlencode($ip));

            if (! $response->successful()) {
                return $empty;
            }

            $data = $response->json();
            if (! is_array($data) || empty($data['success'])) {
                return $empty;
            }

            $city = is_string($data['city'] ?? null) ? $data['city'] : null;
            $region = is_string($data['region'] ?? null) ? $data['region'] : null;
            $country = is_string($data['country'] ?? null) ? $data['country'] : null;
            $isp = null;
            if (isset($data['connection']['isp']) && is_string($data['connection']['isp'])) {
                $isp = $data['connection']['isp'];
            }

            $lat = $data['latitude'] ?? null;
            $lon = $data['longitude'] ?? null;

            $parts = array_filter([$city, $region, $country], fn ($v) => $v !== null && $v !== '');
            $summary = $parts !== [] ? implode(', ', $parts) : null;

            return [
                'summary' => $summary,
                'city' => $city,
                'region' => $region,
                'country' => $country,
                'isp' => $isp,
                'latitude' => $lat,
                'longitude' => $lon,
            ];
        } catch (\Throwable $e) {
            Log::debug('IpLocationLookup failed', ['ip' => $ip, 'message' => $e->getMessage()]);

            return $empty;
        }
    }

    private static function isPrivateOrLocalIp(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
        }

        return true;
    }
}
