<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'brand' => ['sometimes', 'required', 'string', 'max:120'],
            'price' => ['sometimes', 'required', 'integer', 'min:0', 'max:999'],
        ];
    }
}
