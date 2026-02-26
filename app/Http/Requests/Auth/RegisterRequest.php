<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:client,consultant'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'validation.name.required',
            'email.required' => 'validation.email.required',
            'email.email' => 'validation.email.format',
            'email.unique' => 'validation.email.unique',
            'password.required' => 'validation.password.required',
            'password.min' => 'validation.password.min',
            'password.confirmed' => 'validation.password.confirmed',
            'role.in' => 'register.roleInvalid',
        ];
    }
}
