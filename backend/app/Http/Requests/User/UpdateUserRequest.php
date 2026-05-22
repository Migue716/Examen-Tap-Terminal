<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'username' => ['sometimes', 'required', 'email', Rule::unique('users', 'username')->ignore($userId, '_id')],
            'phone' => ['nullable', 'regex:/^\+\d{8,15}$/'],
            'profile_photo' => ['sometimes', 'required', 'string', 'max:500000'],
            'profile_ids' => ['nullable', 'array'],
            'profile_ids.*' => ['string'],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
