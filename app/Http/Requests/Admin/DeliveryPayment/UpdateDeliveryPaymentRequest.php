<?php

namespace App\Http\Requests\Admin\DeliveryPayment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'translations' => 'required|array',
            'translations.*.content' => 'required|string|max:5000',
        ];
    }
}
