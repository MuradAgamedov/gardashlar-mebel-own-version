<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Vacancy;

class VacancyDetailResource extends JsonResource
{
    public int $languageId = 0;

    public function toArray(Request $request): array
    {
        $translation = $this->translates->firstWhere('language_id', $this->languageId);

        $slugTranslations = $this->translates->mapWithKeys(fn($t) => [$t->language->lang_code => $t->slug]);

        $requirements = VacancyRequirementResource::collection(
            $this->requirements()->with(['translates' => fn($q) => $q->where('language_id', $this->languageId)])->active()->ordered()->get()
        );

        $otherVacancies = Vacancy::with([
            'translates' => fn($q) => $q->where('language_id', $this->languageId),
            'requirements.translates' => fn($q) => $q->where('language_id', $this->languageId),
        ])
        ->where('id', '!=', $this->id)
        ->active()
        ->notExpired()
        ->ordered()
        ->limit(5)
        ->get()
        ->map(function ($item) {
            $t = $item->translates->first();
            $firstReq = VacancyRequirementResource::collection($item->requirements)->first();
            return [
                'id' => $item->id,
                'title' => $t?->title ?? '',
                'slug' => $t?->slug ?? '',
                'position' => $t?->position ?? '',
                'experience' => $t?->experience ?? '',
                'work_schedule' => $t?->work_schedule ?? '',
                'salary' => $t?->salary ?? '',
                'email' => $item->email,
                'phone' => $item->phone,
                'age' => $item->age,
                'education' => $t?->education ?? '',
                'related_person' => $t?->related_person ?? '',
                'city' => $t?->city ?? '',
                'last_apply_date' => $item->last_apply_date?->format('Y-m-d'),
                'requirements' => $firstReq ?? [],
            ];
        });

        return [
            'id' => $this->id,
            'title' => $translation?->title ?? '',
            'slug' => $translation?->slug ?? '',
            'position' => $translation?->position ?? '',
            'experience' => $translation?->experience ?? '',
            'work_schedule' => $translation?->work_schedule ?? '',
            'salary' => $translation?->salary ?? '',
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $translation?->address ?? '',
            'age' => $this->age,
            'education' => $translation?->education ?? '',
            'related_person' => $translation?->related_person ?? '',
            'city' => $translation?->city ?? '',
            'last_apply_date' => $this->last_apply_date?->format('Y-m-d'),
            'created_at' => $this->created_at?->toISOString(),
            'seo_title' => $translation?->seo_title ?? '',
            'seo_description' => $translation?->seo_description ?? '',
            'seo_keywords' => $translation?->seo_keywords ?? '',
            'requirements' => $requirements,
            'translates' => ['slug' => $slugTranslations],
            'other_vacancies' => $otherVacancies,
        ];
    }
}
