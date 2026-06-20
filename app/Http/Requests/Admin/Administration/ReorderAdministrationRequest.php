<?php

namespace App\Http\Requests\Admin\Administration;

use Illuminate\Foundation\Http\FormRequest;

class ReorderAdministrationRequest extends FormRequest
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
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:administrations,id'
        ];
    }
}
