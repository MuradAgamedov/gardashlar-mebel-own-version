<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\SeoPageRepositoryInterface;
use App\Interfaces\Services\Front\SeoPageServiceInterface;
use App\Services\BaseService;
use App\Models\SeoPageTranslate;

class SeoPageService extends BaseService implements SeoPageServiceInterface
{
    protected SeoPageRepositoryInterface $seoPageRepository;

    public function __construct(SeoPageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->seoPageRepository = $repository;
    }

    public function getTranslateBySlug(string $slug, int $languageId): ?SeoPageTranslate
    {
        return $this->seoPageRepository->getTranslateBySlug($slug, $languageId);
    }

    public function getSlugTranslations(int $seoPageId): array
    {
        return $this->seoPageRepository->getSlugTranslations($seoPageId);
    }
}
