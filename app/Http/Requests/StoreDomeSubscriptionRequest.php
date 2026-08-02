<?php

namespace App\Http\Requests;

use App\Enums\ClubInterest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDomeSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'submission_uuid' => ['required', 'uuid'], 'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'], 'phone' => ['nullable', 'string', 'max:32'],
            'city' => ['nullable', 'string', 'max:120'], 'position' => ['nullable', 'string', 'max:160'],
            'professional_role' => ['nullable', 'string', 'max:120'], 'interests' => ['nullable', 'array', 'max:20'],
            'interests.*' => ['string', Rule::in(array_column(ClubInterest::cases(), 'value'))], 'consent' => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'full_name' => $this->input('full_name', $this->input('fullname')),
            'professional_role' => $this->input('professional_role', $this->input('role')),
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'consent' => $this->boolean('consent') || $this->boolean('subscribe_newsletter'),
        ]);
    }
}
