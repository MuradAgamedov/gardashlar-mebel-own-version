<?php

namespace App\Http\Requests\Admin\Page;

use Illuminate\Foundation\Http\FormRequest;

class GenerateSlugPageRequest extends FormRequest
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
                'language_id' => 'required|exists:languages,id',
                'page_id' => 'nullable|exists:pages,id',
            ];
    }
}
