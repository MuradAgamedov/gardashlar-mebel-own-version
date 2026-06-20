<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsPostDetailResource extends JsonResource
{
    public int $languageId = 0;

    public function toArray(Request $request): array
    {
        $translation = $this->translates->firstWhere('language_id', $this->languageId);
        $categoryTranslation = $this->category?->translates?->firstWhere('language_id', $this->languageId);

        $slugTranslations = $this->translates->mapWithKeys(fn($t) => [$t->language->lang_code => $t->slug]);
        $categorySlugTranslations = $this->category?->translates?->mapWithKeys(fn($t) => [$t->language->lang_code => $t->slug]) ?? collect();

        $similarNews = \App\Models\NewsPost::published()
            ->with([
                'translates' => fn($q) => $q->where('language_id', $this->languageId),
                'category.translates' => fn($q) => $q->where('language_id', $this->languageId),
            ])
            ->where('news_category_id', $this->news_category_id)
            ->where('id', '!=', $this->id)
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($news) {
                $tr = $news->translates->first();
                $catTr = $news->category?->translates?->first();
                return [
                    'id' => $news->id,
                    'title' => $tr?->title,
                    'slug' => $tr?->slug,
                    'card_image_url' => $news->card_image
                        ? asset('storage/' . $news->card_image)
                        : asset('images/default_image.webp'),
                    'card_description' => $tr?->card_description,
                    'category_slug' => $catTr?->slug,
                ];
            });

        return [
            'id' => $this->id,
            'title' => $translation?->title,
            'slug' => $translation?->slug,
            'description' => $translation?->description,
            'card_image_url' => $this->card_image
                ? asset('storage/' . $this->card_image)
                : asset('images/default_image.webp'),
            'inner_banner_image_url' => $this->inner_banner_image
                ? asset('storage/' . $this->inner_banner_image)
                : asset('images/default_image.webp'),
            'published_at' => $this->published_at,
            'seo_title' => $translation?->seo_title,
            'seo_keywords' => $translation?->seo_keywords,
            'seo_description' => $translation?->seo_description,
            'category' => [
                'id' => $this->category?->id,
                'title' => $categoryTranslation?->title,
                'slug' => $categoryTranslation?->slug,
            ],
            'category_slug' => $categoryTranslation?->slug,
            'images' => $this->images->map(fn($image) => [
                'id' => $image->id,
                'image_url' => $image->image_url
                    ? asset('storage/' . $image->image_url)
                    : asset('images/default_image.webp'),
            ]),
            'similar_news' => $similarNews,
            'translates' => [
                'slug' => $slugTranslations,
                'category_slug' => $categorySlugTranslations,
            ],
        ];
    }
}
