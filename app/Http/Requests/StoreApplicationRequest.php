<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'linkedin_profile' => ['nullable', 'url', 'max:500'],
            'program_interest' => ['required', 'in:accelerator,venture,corporate'],
            'description' => ['required', 'string', 'max:5000'],
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
            'linkedin_profile.url' => 'validation.linkedin.url',
            'program_interest.required' => 'validation.programInterest.required',
            'program_interest.in' => 'validation.programInterest.invalid',
            'description.required' => 'validation.description.required',
        ];
    }
}
