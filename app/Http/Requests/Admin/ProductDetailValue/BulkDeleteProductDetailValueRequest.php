<?php

namespace App\Http\Requests\Admin\ProductDetailValue;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteProductDetailValueRequest extends FormRequest
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
                'ids' => 'required|array',
                'ids.*' => 'exists:product_detail_values,id'
            ];
    }
}
