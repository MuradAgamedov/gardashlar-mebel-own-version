<?php

namespace App\Http\Requests\Admin\ServiceAdvantage;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderServiceAdvantageRequest extends FormRequest
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
            'advantages' => 'required|array',
            'advantages.*.id' => 'required|exists:service_advantages,id',
            'advantages.*.order' => 'required|integer|min:0'
        ];
    }
}
