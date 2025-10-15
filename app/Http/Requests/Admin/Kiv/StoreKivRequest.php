<?php

namespace App\Http\Requests\Admin\Kiv;

use Illuminate\Foundation\Http\FormRequest;

class StoreKivRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'video_link' => 'nullable|string|max:500',
            'kiv_category_id' => 'required|exists:kiv_categories,id',
            'status' => 'boolean',
        ];
    }
}
