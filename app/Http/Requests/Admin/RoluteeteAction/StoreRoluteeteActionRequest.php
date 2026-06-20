<?php

namespace App\Http\Requests\Admin\RoluteeteAction;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoluteeteActionRequest extends FormRequest
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
            'percent' => 'nullable|numeric|min:1|max:100',
            'image' => 'nullable|image|max:5120',
            "percent_value" => "nullable"
        ];
    }
}
