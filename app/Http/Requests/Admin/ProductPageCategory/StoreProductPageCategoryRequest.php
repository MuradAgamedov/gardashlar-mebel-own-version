<?php

namespace App\Http\Requests\Admin\ProductPageCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductPageCategoryRequest extends FormRequest
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
            'product_category_id' => 'required|exists:product_categories,id|unique:product_page_categories,product_category_id',
            'status' => 'boolean'
        ];
    }
}
