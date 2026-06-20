<?php

namespace App\Http\Requests\Admin\ProductPageCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductPageCategoryRequest extends FormRequest
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
        $productPageCategory = $this->route('productPageCategory');
        $productPageCategoryId = is_object($productPageCategory) ? $productPageCategory->id : $productPageCategory;

        return [
            'product_category_id' => 'required|exists:product_categories,id|unique:product_page_categories,product_category_id,' . $productPageCategoryId,
            'status' => 'boolean'
        ];
    }
}
