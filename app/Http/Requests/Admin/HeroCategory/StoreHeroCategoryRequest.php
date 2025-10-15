<?php

namespace App\Http\Requests\Admin\HeroCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreHeroCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_category_id' => 'required|exists:product_categories,id|unique:hero_categories,product_category_id',
            'status' => 'boolean'
        ];
    }
}
