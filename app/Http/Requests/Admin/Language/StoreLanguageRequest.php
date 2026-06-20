<?php

namespace App\Http\Requests\Admin\Language;

use Illuminate\Foundation\Http\FormRequest;

class StoreLanguageRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'lang_code' => 'required|string|max:10|unique:languages',
            'lang_name' => 'required|string|max:255',
            'status' => 'boolean',
            'is_main_language' => 'boolean'
        ];
    }
}
