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
        $heroProductId = $this->route('hero_product')->id;
        
        return [
            'product_id' => 'required|exists:products,id|unique:hero_products,product_id,' . $heroProductId,
            'status' => 'boolean'
        ];
    }
}
