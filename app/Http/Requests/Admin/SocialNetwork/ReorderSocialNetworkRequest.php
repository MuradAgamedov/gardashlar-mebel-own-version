<?php

namespace App\Http\Requests\Admin\SocialNetwork;

use Illuminate\Foundation\Http\FormRequest;

class ReorderSocialNetworkRequest extends FormRequest
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
            'orders' => 'required|array',
            'orders.*' => 'integer|exists:social_networks,id'
        ];
    }
}
