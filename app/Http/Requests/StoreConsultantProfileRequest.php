<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultantProfileRequest extends FormRequest
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
            'bio_en' => ['required', 'string', 'min:50', 'max:5000'],
            'bio_ar' => ['nullable', 'string', 'max:5000'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:50'],
            'hourly_rate' => ['required', 'numeric', 'min:50', 'max:10000'],
            'languages' => ['required', 'array', 'min:1'],
            'languages.*' => ['string', 'max:10'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'response_time_hours' => ['nullable', 'integer', 'min:1', 'max:72'],
            'calendly_event_type_url' => ['required', 'url', 'max:500'],
            'specialization_ids' => ['required', 'array', 'min:1', 'max:5'],
            'specialization_ids.*' => ['exists:specializations,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bio_en.min' => 'validation.bioEn.min',
            'calendly_event_type_url.required' => 'validation.calendlyUrl.required',
            'specialization_ids.required' => 'validation.specializations.required',
        ];
    }
}
