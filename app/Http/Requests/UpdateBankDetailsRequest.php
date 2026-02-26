<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBankDetailsRequest extends FormRequest
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
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_account_holder_name' => ['required', 'string', 'max:255'],
            'iban' => ['required', 'string', 'regex:/^SA\d{22}$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bank_name.required' => 'validation.bankName.required',
            'bank_account_holder_name.required' => 'validation.accountHolder.required',
            'iban.required' => 'validation.iban.required',
            'iban.regex' => 'validation.iban.format',
        ];
    }
}
