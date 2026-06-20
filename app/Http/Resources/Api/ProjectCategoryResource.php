<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectCategoryResource extends JsonResource
{
    public int $languageId = 0;

    public function toArray(Request $request): array
    {
        $translation = $this->translates->where('language_id', $this->languageId)->first()
            ?? $this->translates->first();

        $slugTranslations = $this->translates->mapWithKeys(fn($t) => [$t->language->lang_code ?? $t->language_id => $t->slug]);

        return [
            'id' => $this->id,
            'title' => $translation?->title,
            'slug' => $translation?->slug,
            'seo_title' => $translation?->seo_title,
            'seo_keywords' => $translation?->seo_keywords,
            'seo_description' => $translation?->seo_description,
            'image_alt_text' => $translation?->image_alt_text,
            'image_url' => $this->image_url,
            'translates' => ['slug' => $slugTranslations],
        ];
    }
}
