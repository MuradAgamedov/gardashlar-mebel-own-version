<?php

namespace App\Http\Requests\Admin\PartnerSlider;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartnerSliderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'status' => 'nullable|boolean',
            'link' => 'nullable|url',
            'translations.*.title' => 'required|string|max:255',
        ];
    }
}
