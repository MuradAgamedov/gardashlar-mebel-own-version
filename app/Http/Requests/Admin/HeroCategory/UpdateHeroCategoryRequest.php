<?php

namespace App\Http\Requests\Admin\HeroCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHeroCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_category_id' => 'required|exists:product_categories,id|unique:hero_categories,product_category_id,' . $this->heroCategory->id,
            'status' => 'boolean'
        ];
    }
}
