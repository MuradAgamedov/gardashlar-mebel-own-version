<?php

namespace App\Http\Requests\Admin\Dictionary;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDictionaryRequest extends FormRequest
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
        $dictionary = $this->route('dictionary');
        $dictionaryId = is_object($dictionary) ? $dictionary->id : $dictionary;

        return [
            'keyword' => 'required|string|max:255|unique:dictionaries,keyword,' . $dictionaryId,
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.value' => 'required|string',
        ];
    }
}
