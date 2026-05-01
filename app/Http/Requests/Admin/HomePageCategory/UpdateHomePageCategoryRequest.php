<?php

namespace App\Http\Requests\Admin\HomePageCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomePageCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_category_id' => 'required|exists:product_categories,id|unique:home_page_categories,product_category_id,' . $this->route('home_page_category')->id,
            'status' => 'boolean'
        ];
    }
}
