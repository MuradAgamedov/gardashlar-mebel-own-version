<?php

namespace App\Http\Requests\Admin\HeroProduct;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHeroProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id|unique:hero_products,product_id,' . $this->heroProduct->id,
            'status' => 'boolean'
        ];
    }
}
