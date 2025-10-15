<?php

namespace App\Http\Requests\Admin\ProductColor;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductColorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'color_code' => 'required|string|unique:product_colors,color_code'
        ];
    }
}
