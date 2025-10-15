<?php

namespace App\Http\Requests\Admin\Certificate;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'date' => 'required|string|max:255',
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.image_alt_text' => 'required|string|max:255',
            'translations.*.title' => 'required|string|max:255',
        ];
    }
}
