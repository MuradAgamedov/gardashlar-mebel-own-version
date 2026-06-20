<?php

namespace App\Http\Requests\Admin\BecomeAInvestorAdvantage;

use Illuminate\Foundation\Http\FormRequest;

class ReorderBecomeAInvestorAdvantageRequest extends FormRequest
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
            'advantages.*' => 'required|integer|exists:become_a_investor_advantages,id',
        ];
    }
}
