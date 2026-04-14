<?php

namespace App\Http\Requests;

use App\Enums\ClubInterest;
use App\Enums\Sector;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscribeToNewsletterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^(?:\+?966|0)?5\d{8}$/'],
            'position' => ['required', 'string', 'max:100'],
            'interests' => ['required', 'array'],
            'interests.*' => ['string', Rule::in(array_column(ClubInterest::cases(), 'value'))],
            'city' => ['required', 'string', 'max:100'],
            'sector' => ['required', 'string', Rule::in(array_column(Sector::cases(), 'value'))],
            'subscribe_newsletter' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fullname.required' => 'Please enter your full name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Please enter your mobile number.',
            'phone.regex' => 'Please enter a valid Saudi mobile number.',
            'interests.*.in' => 'One or more selected interests are invalid.',
            'sector.in' => 'Please select a valid sector.',
        ];
    }
}
