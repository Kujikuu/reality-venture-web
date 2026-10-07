<?php

namespace App\Http\Requests;

use App\Rules\Turnstile;
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
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:applications,email'],
            'phone' => ['required', 'string', 'regex:/^(?:\+?966|0)?5\d{8}$/'],
            'city' => ['nullable', 'string', 'max:255'],
            'social_profile' => ['nullable', 'url', 'max:500'],
            'description' => ['required', 'string', 'max:5000'],

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

            'captcha_token.required' => 'validation.captcha.required',
        ];
    }
}
