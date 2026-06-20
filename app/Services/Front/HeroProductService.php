<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\HeroProductRepositoryInterface;
use App\Interfaces\Services\Front\HeroProductServiceInterface;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;

class HeroProductService extends BaseService implements HeroProductServiceInterface
{
    protected HeroProductRepositoryInterface $heroProductRepository;

    public function __construct(HeroProductRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->heroProductRepository = $repository;
    }

    public function getActiveOrderedByCategory(int $categoryId, int $languageId): Collection
    {
        return $this->heroProductRepository->getActiveOrderedByCategory($categoryId, $languageId);
    }
}
