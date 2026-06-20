<?php

namespace App\Http\Requests\Admin\ProductAccordion;

use Illuminate\Foundation\Http\FormRequest;

class ReorderProductAccordionRequest extends FormRequest
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
            'items' => 'required|array',
            'items.*' => 'exists:product_accordions,id'
        ];
    }
}
