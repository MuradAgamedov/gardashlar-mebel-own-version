<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostDetailResource extends JsonResource
{
    public int $languageId = 0;

    public function toArray(Request $request): array
    {
        $translation = $this->translates->firstWhere('language_id', $this->languageId);
        $categoryTranslation = $this->category?->translates?->firstWhere('language_id', $this->languageId);

        $slugTranslations = $this->translates->mapWithKeys(fn($t) => [$t->language->lang_code => $t->slug]);
        $categorySlugTranslations = $this->category?->translates?->mapWithKeys(fn($t) => [$t->language->lang_code => $t->slug]) ?? collect();

        $similarBlogs = \App\Models\BlogPost::published()
            ->with([
                'translates' => fn($q) => $q->where('language_id', $this->languageId),
                'category.translates' => fn($q) => $q->where('language_id', $this->languageId),
            ])
            ->where('blog_category_id', $this->blog_category_id)
            ->where('id', '!=', $this->id)
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get()
            ->map(function ($blog) {
                $tr = $blog->translates->first();
                $catTr = $blog->category?->translates?->first();
                return [
                    'id' => $blog->id,
                    'title' => $tr?->title,
                    'slug' => $tr?->slug,
                    'card_image_url' => $blog->card_image
                        ? asset('storage/' . $blog->card_image)
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
            'similar_blogs' => $similarBlogs,
            'translates' => [
                'slug' => $slugTranslations,
                'category_slug' => $categorySlugTranslations,
            ],
        ];
    }
}
