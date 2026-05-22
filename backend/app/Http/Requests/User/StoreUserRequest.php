<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'email', Rule::unique('users', 'username')],
            'phone' => ['nullable', 'regex:/^\+\d{8,15}$/'],
            'profile_photo' => ['required', 'string', 'max:500000'],
            'profile_ids' => ['nullable', 'array'],
            'profile_ids.*' => ['string'],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
