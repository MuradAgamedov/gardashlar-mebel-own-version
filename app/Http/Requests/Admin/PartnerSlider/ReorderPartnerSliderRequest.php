<?php

namespace App\Http\Requests\Admin\PartnerSlider;

use Illuminate\Foundation\Http\FormRequest;

class ReorderPartnerSliderRequest extends FormRequest
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
            'sliders' => 'required|array',
            'sliders.*' => 'required|integer|exists:partner_sliders,id',
        ];
    }
}
