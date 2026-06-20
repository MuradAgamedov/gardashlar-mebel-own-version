<?php

namespace App\Http\Requests\Admin\Dictionary;

use Illuminate\Foundation\Http\FormRequest;

class StoreDictionaryRequest extends FormRequest
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
            'keyword' => 'required|string|max:255|unique:dictionaries',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.value' => 'required|string',
        ];
    }
}
