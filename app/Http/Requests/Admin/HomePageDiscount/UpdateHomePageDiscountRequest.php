<?php

namespace App\Http\Requests\Admin\HomePageDiscount;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomePageDiscountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'translations.*.title' => 'required|string|max:255',
            'translations.*.subtitle' => 'required|string|max:255',
            'translations.*.button_link' => 'required|string|max:255',
            'bg_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
}
