<?php

namespace App\Http\Requests\Admin\Kiv;

use Illuminate\Foundation\Http\FormRequest;

class ReorderKivRequest extends FormRequest
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
            'kivs' => 'required|array',
            'kivs.*' => 'required|integer|exists:kivs,id',
        ];
    }
}
