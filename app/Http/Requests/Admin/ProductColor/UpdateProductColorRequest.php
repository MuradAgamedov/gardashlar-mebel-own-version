<?php

namespace App\Http\Requests\Admin\ProductColor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductColorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'color_code' => 'required|string|unique:product_colors,color_code,' . $this->route('productColor')->id
        ];
    }
}
