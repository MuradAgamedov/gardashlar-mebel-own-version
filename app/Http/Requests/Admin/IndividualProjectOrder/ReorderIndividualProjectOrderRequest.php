<?php

namespace App\Http\Requests\Admin\IndividualProjectOrder;

use Illuminate\Foundation\Http\FormRequest;

class ReorderIndividualProjectOrderRequest extends FormRequest
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
            'order.*' => 'required|integer|exists:individual_project_orders,id'
        ];
    }
}
