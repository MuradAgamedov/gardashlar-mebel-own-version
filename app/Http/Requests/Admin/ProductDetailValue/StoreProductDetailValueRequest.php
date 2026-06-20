<?php

namespace App\Http\Requests\Admin\ProductDetailValue;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductDetailValueRequest extends FormRequest
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
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.keyword' => 'required|string|max:255',
        ];
    }
}
