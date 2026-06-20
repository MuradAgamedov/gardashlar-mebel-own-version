<?php

namespace App\Http\Requests\Admin\ServiceAdvantage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceAdvantageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {

        return [
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'translates' => 'required|array|min:1',
            'translates.*.language_id' => 'required|exists:languages,id',
            'translates.*.title' => 'required|string|max:255',
            'translates.*.icon_alt_text' => 'nullable|string|max:255',
            'translates.*.description' => 'required|string',
        ], [
            'translates.required' => 'At least one language translation is required.',
            'translates.*.title.required' => 'Title is required for all languages.',
            'translates.*.description.required' => 'Description is required for all languages.',
        ];
    }
}
