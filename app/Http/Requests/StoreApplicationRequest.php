<?php

namespace App\Http\Requests;

use App\Rules\Turnstile;
use App\Services\SniperHubIntake;
use App\Support\ApplicantPhone;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        // Sniper Hub keeps a second application from the same email and
        // warns its admins, so the unique rule only applies to this app.
        $toSniperHub = SniperHubIntake::isEnabled();

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', ...($toSniperHub ? [] : ['unique:applications,email'])],
            // Sniper Hub takes international numbers too, Saudi first.
            'phone' => $toSniperHub
                ? ['required', 'string', 'max:20', function (string $attribute, mixed $value, Closure $fail): void {
                    if (! is_string($value) || ! ApplicantPhone::isValid($value)) {
                        $fail('validation.phone.formatInternational');
                    }
                }]
                : ['required', 'string', 'regex:/^(?:\+?966|0)?5\d{8}$/'],
            'city' => ['nullable', 'string', 'max:255'],
            'social_profile' => ['nullable', 'url', 'max:500'],
            'description' => ['required', 'string', 'max:5000'],

            'consent' => $toSniperHub ? ['accepted'] : ['nullable'],
            'submission_id' => ['nullable', 'string', 'max:255'],

            'captcha_token' => [Turnstile::isEnabled() ? 'required' : 'nullable', 'string', 'max:2048', new Turnstile($this->ip())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'validation.firstName.required',
            'last_name.required' => 'validation.lastName.required',
            'email.required' => 'validation.email.required',
            'email.email' => 'validation.email.format',
            'email.unique' => 'validation.email.unique',
            'phone.required' => 'validation.phone.required',
            'phone.regex' => 'validation.phone.format',
            'social_profile.url' => 'validation.linkedin.url',
            'description.required' => 'validation.description.required',

            'consent.accepted' => 'validation.consent.required',

            'captcha_token.required' => 'validation.captcha.required',
        ];
    }
}
