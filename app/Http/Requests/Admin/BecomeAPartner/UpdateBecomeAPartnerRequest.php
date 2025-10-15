<?php

namespace App\Http\Requests\Admin\BecomeAPartner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBecomeAPartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'translations' => 'required|array',
            'translations.*.content' => 'required|string|max:5000',
        ];
    }
}
