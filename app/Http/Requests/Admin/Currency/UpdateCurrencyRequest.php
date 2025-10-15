<?php

namespace App\Http\Requests\Admin\Currency;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currencyId = $this->route('currency')->id ?? null;

        return [
            'code' => 'required|string|max:3|unique:currencies,code,' . $currencyId,
            'course' => 'required|numeric|min:0.0001|max:999999.9999',
            'status' => 'boolean',
        ];
    }
}
