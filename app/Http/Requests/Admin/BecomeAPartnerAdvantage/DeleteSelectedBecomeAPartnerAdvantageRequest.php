<?php

namespace App\Http\Requests\Admin\BecomeAPartnerAdvantage;

use Illuminate\Foundation\Http\FormRequest;

class DeleteSelectedBecomeAPartnerAdvantageRequest extends FormRequest
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
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:become_a_partner_advantages,id'
        ];
    }
}
