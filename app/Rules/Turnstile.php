<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Turnstile implements ValidationRule
{
    public function __construct(
        private readonly ?string $remoteIp = null,
    ) {}

    public static function isEnabled(): bool
    {
        return filled(config('services.turnstile.secret_key'));
    }

    /**
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::isEnabled()) {
            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post(config('services.turnstile.verify_url'), array_filter([
                    'secret' => config('services.turnstile.secret_key'),
                    'response' => (string) $value,
                    'remoteip' => $this->remoteIp,
                ]));
        } catch (ConnectionException $e) {
            Log::warning('Turnstile verification could not connect.', ['error' => $e->getMessage()]);
            $fail('validation.captcha.failed');

            return;
        }

        if (! $response->successful() || $response->json('success') !== true) {
            Log::info('Turnstile verification failed.', [
                'status' => $response->status(),
                'error_codes' => $response->json('error-codes'),
            ]);
            $fail('validation.captcha.failed');
        }
    }
}
