<?php

namespace App\Http\Requests\Admin\Subscriber;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteSubscriberRequest extends FormRequest
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
            'subscriber_ids' => 'required|array',
            'subscriber_ids.*' => 'exists:subscribers,id'
        ];
    }
}
