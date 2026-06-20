<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\SeoPageRepositoryInterface;
use App\Models\SeoPage;
use App\Models\SeoPageTranslate;
use App\Repositories\BaseRepository;

class SeoPageRepository extends BaseRepository implements SeoPageRepositoryInterface
{
    public function __construct(SeoPage $model)
    {
        parent::__construct($model);
    }

    public function getTranslateBySlug(string $slug, int $languageId): ?SeoPageTranslate
    {
        return SeoPageTranslate::with(['seoPage.category.translates', 'language'])
            ->where('slug', $slug)
            ->where('language_id', $languageId)
            ->first();
    }

    public function getSlugTranslations(int $seoPageId): array
    {
        return SeoPageTranslate::where('seo_page_id', $seoPageId)
            ->with('language')
            ->get()
            ->mapWithKeys(function ($translation) {
                return [
                    $translation->language->lang_code => $translation->slug
                ];
            })
            ->filter(function ($slug) {
                return !empty($slug);
            })
            ->toArray();
    }
}
