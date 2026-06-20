<?php

namespace App\Http\Requests\Admin\OurGoal;

use Illuminate\Foundation\Http\FormRequest;

class ReorderOurGoalRequest extends FormRequest
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
            'items' => 'required|array',
            'items.*' => 'integer|exists:our_goals,id'
        ];
    }
}
