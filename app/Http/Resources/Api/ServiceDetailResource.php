<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceDetailResource extends JsonResource
{
    public int $languageId = 0;

    public function toArray(Request $request): array
    {
        $translation = $this->languageId
            ? $this->translates->firstWhere('language_id', $this->languageId)
            : $this->translates->first();

        $slugTranslations = $this->translates->mapWithKeys(fn($t) => [$t->language->lang_code => $t->slug]);

        return [
            'id' => $this->id,
            'slug' => $translation?->slug,
            'title' => $translation?->title ?? '',
            'card_description' => $translation?->card_description ?? '',
            'main_description' => $translation?->main_description ?? '',
            'form_top_text' => $translation?->form_top_text ?? '',
            'advantages_title' => $translation?->advantages_title ?? '',
            'seo_title' => $translation?->seo_title ?? '',
            'seo_keywords' => $translation?->seo_keywords ?? '',
            'seo_description' => $translation?->seo_description ?? '',
            'advantages' => ServiceAdvantageResource::collection($this->advantages),
            'translates' => [
                'slug' => $slugTranslations,
            ],
        ];
    }
}
