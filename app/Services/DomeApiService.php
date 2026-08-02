<?php

namespace App\Services;

use App\Exceptions\DomeIntegrationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class DomeApiService
{
    /** @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function subscribe(array $payload): array
    {
        return $this->post('subscribers', $payload);
    }

    /** @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function apply(array $payload): array
    {
        return $this->post('membership-applications', $payload);
    }

    public function hasAccess(string $email): bool
    {
        return (bool) ($this->post('access/check', ['email' => $email])['eligible'] ?? false);
    }

    /** @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function post(string $endpoint, array $payload): array
    {
        try {
            $response = $this->client()->post($endpoint, $payload);
            if ($response->successful()) {
                return $response->json('data', []);
            }
            if ($response->status() === 422) {
                throw new DomeIntegrationException('validation', $response->json('errors', []), 422);
            }

            $reason = match ($response->status()) {
                401, 403 => 'unauthorized', 409 => 'conflict', 429 => 'rate_limited',
                default => $response->serverError() ? 'unavailable' : 'failed',
            };
            throw new DomeIntegrationException($reason, status: $response->status());
        } catch (DomeIntegrationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('DOME™ API request failed', ['endpoint' => $endpoint, 'exception' => $exception::class, 'message' => $exception->getMessage()]);
            throw new DomeIntegrationException('unavailable', status: 503);
        }
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl(rtrim((string) config('services.dome.url'), '/').'/api/v1')
            ->withToken((string) config('services.dome.token'))->acceptJson()->asJson()
            ->connectTimeout((int) config('services.dome.connect_timeout', 3))->timeout((int) config('services.dome.timeout', 8))
            ->retry([150, 300], fn (Throwable $exception): bool => $exception instanceof ConnectionException, throw: false);
    }
}
