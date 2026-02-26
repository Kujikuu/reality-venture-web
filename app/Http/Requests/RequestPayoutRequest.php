<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestPayoutRequest extends FormRequest
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
        $minimum = config('marketplace.minimum_payout_amount', 100);

        return [
            'amount' => ['required', 'numeric', 'min:'.$minimum],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'validation.amount.required',
            'amount.numeric' => 'validation.amount.numeric',
            'amount.min' => 'validation.amount.min',
        ];
    }
}
