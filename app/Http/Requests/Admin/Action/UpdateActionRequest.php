<?php

namespace App\Http\Requests\Admin\Action;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|exists:companies,id',
            'icon' => 'nullable|string|max:255',
            'status' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'translates' => 'required|array',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.description' => 'nullable|string',
            'translates.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'translates.*.image_alt_text' => 'nullable|string|max:255',
        ];
    }
}
