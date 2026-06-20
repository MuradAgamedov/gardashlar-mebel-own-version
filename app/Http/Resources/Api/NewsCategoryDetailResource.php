<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsCategoryDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translates->first();

        $slugTranslations = $this->translates->mapWithKeys(function ($t) {
            return [$t->language->lang_code => $t->slug];
        });

        return [
            'id' => $this->id,
            'title' => $translation?->title,
            'slug' => $translation?->slug,
            'seo_title' => $translation?->seo_title,
            'seo_keywords' => $translation?->seo_keywords,
            'seo_description' => $translation?->seo_description,
            'posts_count' => $this->news_posts_count ?? 0,
            'translates' => [
                'slug' => $slugTranslations,
            ],
        ];
    }
}
