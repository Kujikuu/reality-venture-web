<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class CalendlyService
{
    public function extractUsernameFromApiUri(string $uri): ?string
    {
        if (preg_match('#/users/([^/]+)#', $uri, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function extractUsernameFromPublicUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $parsed = parse_url($url, PHP_URL_PATH);

        if (! $parsed) {
            return null;
        }

        $segments = array_values(array_filter(explode('/', $parsed)));

        return $segments[0] ?? null;
    }

    public function cancelEvent(?string $eventUuid, string $reason = 'Cancelled via platform'): void
    {
        if (! $eventUuid) {
            return;
        }

        $token = config('marketplace.calendly.api_token');

        if (! $token) {
            return;
        }

        try {
            $client = new Client;
            $client->post("https://api.calendly.com/scheduled_events/{$eventUuid}/cancellation", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                ],
                'json' => ['reason' => $reason],
            ]);
        } catch (\Exception $e) {
            Log::warning('Calendly event cancellation failed', [
                'event_uuid' => $eventUuid,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
