<?php

namespace App\Http\Requests\Admin\BecomeAPartnerAdvantage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBecomeAPartnerAdvantageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.icon_alt_text' => 'required|string|max:255',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string|max:1000',
        ];
    }
}
