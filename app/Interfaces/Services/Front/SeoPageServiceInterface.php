<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;
use App\Models\SeoPageTranslate;

interface SeoPageServiceInterface extends BaseServiceInterface
{
    public function getTranslateBySlug(string $slug, int $languageId): ?SeoPageTranslate;
    public function getSlugTranslations(int $seoPageId): array;
}
