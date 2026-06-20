<?php

namespace App\Http\Requests\Admin\AllSeoPages;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAllSeoPagesRequest extends FormRequest
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
            'is_indexed' => 'nullable|boolean',
            'is_followed' => 'nullable|boolean',
            'rolette_actions' => 'nullable|boolean',
            'popup_ads_show' => 'nullable|boolean',
            'page_header' => 'nullable|string',
            'page_footer' => 'nullable|string',
        ];
    }
}
