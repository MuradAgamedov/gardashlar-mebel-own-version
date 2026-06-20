<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VacancyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();
        $firstRequirement = VacancyRequirementResource::collection($this->requirements)->first();

        $lastApplyDate = $this->last_apply_date?->format('Y-m-d');
        $isExpired = $this->last_apply_date ? now()->greaterThan($this->last_apply_date) : false;

        return [
            'id' => $this->id,
            'title' => $translation?->title ?? '',
            'slug' => $translation?->slug ?? '',
            'position' => $translation?->position ?? '',
            'experience' => $translation?->experience ?? '',
            'salary' => $translation?->salary,
            'age' => $this->age,
            'education' => $translation?->education ?? '',
            'related_person' => $translation?->related_person ?? '',
            'city' => $translation?->city ?? '',
            'email' => $this->email,
            'phone' => $this->phone,
            'last_apply_date' => $lastApplyDate,
            'is_expired' => $isExpired,
            'requirements' => $firstRequirement ?? [],
        ];
    }
}
