<?php

namespace App\Http\Requests\Admin\OurGoal;

use Illuminate\Foundation\Http\FormRequest;

class StoreOurGoalRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status' => 'boolean',
            'translations' => 'required|array',
            'translations.*.language_id' => 'required|exists:languages,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
        ];
    }
}
