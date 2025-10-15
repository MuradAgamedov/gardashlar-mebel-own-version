<?php

namespace App\Http\Requests\Admin\Currency;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:3|unique:currencies,code',
            'course' => 'required|numeric|min:0.0001|max:999999.9999',
            'status' => 'boolean',
        ];
    }
}
