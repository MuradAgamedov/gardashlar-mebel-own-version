<?php

namespace App\Http\Requests\Admin\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $campaign = $this->route('campaign');
        $companyId = is_object($campaign) ? $campaign->id : $campaign;

        return [
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('company_translates', 'slug')->ignore($companyId, 'company_id')
            ],
            'translates.*.card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.card_image_alt_text' => 'nullable|string|max:255',
            'translates.*.inner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.inner_image_alt_text' => 'nullable|string|max:255',
            'translates.*.description' => 'required|string',
            'status' => 'boolean'
        ];
    }
}
