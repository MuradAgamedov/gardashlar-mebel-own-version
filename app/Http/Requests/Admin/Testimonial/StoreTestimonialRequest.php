<?php

namespace App\Http\Requests\Admin\Testimonial;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_image'           => 'nullable|image|max:2048',
            'company_logo'             => 'nullable|image|max:2048',
            'status'                   => 'required|boolean',
            'translations'             => 'required|array',
            'translations.*.full_name' => 'required|string|max:255',
            'translations.*.position'  => 'required|string|max:255',
            'translations.*.comment'   => 'required|string|max:1000',
        ];
    }
}
