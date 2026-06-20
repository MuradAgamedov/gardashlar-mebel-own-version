<?php

namespace App\Interfaces\Repositories\Front;

use App\Interfaces\Repositories\BaseRepositoryInterface;
use App\Models\SeoPageTranslate;

interface SeoPageRepositoryInterface extends BaseRepositoryInterface
{
    public function getTranslateBySlug(string $slug, int $languageId): ?SeoPageTranslate;
    public function getSlugTranslations(int $seoPageId): array;
}
