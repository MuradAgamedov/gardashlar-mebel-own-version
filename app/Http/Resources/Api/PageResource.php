<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public int $languageId = 0;

    public function toArray(Request $request): array
    {
        $translation = $this->translates->firstWhere('language_id', $this->languageId);

        $slugTranslations = $this->translates->mapWithKeys(fn($t) => [$t->language->lang_code => $t->slug]);

        return [
            'id' => $this->id,
            'keyword' => $this->keyword,
            'title' => $translation?->title,
            'slug' => $translation?->slug,
            'seo_title' => $translation?->seo_title,
            'seo_keywords' => $translation?->seo_keywords,
            'seo_description' => $translation?->seo_description,
            'opengraph_title' => $translation?->opengraph_title,
            'opengraph_description' => $translation?->opengraph_description,
            'opengraph_image' => $this->opengraph_image ? asset('storage/' . $this->opengraph_image) : null,
            'opengraph_image_width' => $this->opengraph_image_width,
            'opengraph_image_height' => $this->opengraph_image_height,
            'opengraph_image_alt_text' => $translation?->opengraph_image_alt_text,
            'translates' => ['slug' => $slugTranslations],
        ];
    }
}
