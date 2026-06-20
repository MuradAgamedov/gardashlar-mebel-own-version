<?php

namespace App\Http\Requests\Admin\ProductPriceFilter;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductPriceFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {

        return [
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'translates' => 'required|array',
            'translates.*.title' => 'required|string|max:255',
            'status' => 'boolean'
        ];
    }
}
