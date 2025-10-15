<?php

namespace App\Http\Requests\Admin\AddressType;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.title' => 'required|string|max:255',
        ];
    }
}
