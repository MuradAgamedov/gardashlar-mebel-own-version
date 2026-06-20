<?php

namespace App\Http\Requests\Admin\CreditCard;

use Illuminate\Foundation\Http\FormRequest;

class ReorderCreditCardRequest extends FormRequest
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
            'credit_cards' => 'required|array',
            'credit_cards.*' => 'required|integer|exists:credit_cards,id',
        ];
    }
}
