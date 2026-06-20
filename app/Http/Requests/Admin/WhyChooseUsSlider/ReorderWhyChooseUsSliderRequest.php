<?php

namespace App\Http\Requests\Admin\WhyChooseUsSlider;

use Illuminate\Foundation\Http\FormRequest;

class ReorderWhyChooseUsSliderRequest extends FormRequest
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
            'sliders.*' => 'required|integer|exists:why_choose_us_sliders,id',
        ];
    }
}
