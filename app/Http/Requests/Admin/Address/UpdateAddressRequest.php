<?php

namespace App\Http\Requests\Admin\Address;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address_type_id' => 'required|exists:address_types,id',
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.address' => 'required|string|max:1000',
        ];
    }
}
