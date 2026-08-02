<?php

namespace App\Http\Requests;

use App\Enums\ClubInterest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDomeApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'submission_uuid' => ['required', 'uuid'], 'holder_type' => ['required', Rule::in(['individual', 'company'])],
            'requested_tier' => ['required', Rule::in(['connect', 'engage'])], 'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'], 'phone' => ['required', 'string', 'max:32'],
            'city' => ['required', 'string', 'max:120'], 'position' => ['nullable', 'string', 'max:160'],
            'professional_role' => ['nullable', 'string', 'max:120'], 'interests' => ['nullable', 'array', 'max:20'],
            'interests.*' => ['string', Rule::in(array_column(ClubInterest::cases(), 'value'))],
            'joining_motivation' => ['required', 'string', 'min:20', 'max:3000'],
            'organization_name' => ['nullable', 'required_if:holder_type,company', 'string', 'max:255'],
            'organization_type' => ['nullable', 'string', 'max:120'], 'industry' => ['nullable', 'string', 'max:160'],
            'website_url' => ['nullable', 'url:http,https', 'max:500'], 'consent' => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => mb_strtolower(trim((string) $this->input('email'))), 'consent' => $this->boolean('consent')]);
    }
}
