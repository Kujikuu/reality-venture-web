<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

/**
 * Sends stage 1 to the Sniper Hub intake API, which creates the application
 * in the Reality Venture workspace and sends the confirmation email.
 *
 * The idempotency key comes from the form, so a double click or a retry
 * never creates a second application.
 */
class SniperHubIntake
{
    public static function isEnabled(): bool
    {
        return (bool) config('services.sniperhub.intake_enabled')
            && filled(config('services.sniperhub.intake_url'))
            && filled(config('services.sniperhub.intake_key'));
    }

    /**
     * Where an applicant whose UID this app doesn't hold should go: Hub's
     * access page for RV. Null until the intake is on and the URL is set.
     */
    public static function applicantAccessUrl(): ?string
    {
        $base = config('services.sniperhub.applicant_url');

        return self::isEnabled() && is_string($base) && $base !== ''
            ? rtrim($base, '/').'/access'
            : null;
    }

    /**
     * @param  array<string, mixed>  $data  validated stage 1 fields
     * @return string the application UID
     *
     * @throws ValidationException when Sniper Hub refuses a field
     * @throws RuntimeException when Sniper Hub can't be reached or fails
     */
    public function submit(array $data, string $locale, string $idempotencyKey, ?string $sourceSite = null): string
    {
        try {
            $response = Http::withToken((string) config('services.sniperhub.intake_key'))
                ->withHeaders(['Idempotency-Key' => $idempotencyKey])
                ->acceptJson()
                ->asJson()
                ->timeout((int) config('services.sniperhub.timeout'))
                ->retry(2, 500, fn (Throwable $exception): bool => $exception instanceof ConnectionException
                    || ($exception instanceof RequestException && $exception->response->serverError()), throw: false)
                ->post((string) config('services.sniperhub.intake_url'), [
                    ...$data,
                    'consent' => (bool) ($data['consent'] ?? false),
                    'locale' => $locale,
                    'source_site' => $sourceSite,
                ]);
        } catch (ConnectionException $exception) {
            Log::error('Sniper Hub intake could not connect.', ['error' => $exception->getMessage()]);

            throw new RuntimeException('Sniper Hub intake could not connect.', previous: $exception);
        }

        if ($response->status() === 422) {
            /** @var array<string, list<string>> $errors */
            $errors = (array) $response->json('errors', []);

            throw ValidationException::withMessages(array_map(fn (array $messages): string => (string) ($messages[0] ?? ''), $errors));
        }

        $uid = $response->json('data.uid');

        if (! $response->successful() || ! is_string($uid)) {
            Log::error('Sniper Hub intake refused the application.', ['status' => $response->status(), 'body' => $response->json('message')]);

            throw new RuntimeException("Sniper Hub intake answered {$response->status()}.");
        }

        return $uid;
    }
}
