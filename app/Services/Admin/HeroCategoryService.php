<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\HeroCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\HeroCategoryServiceInterface;
use App\Services\BaseService;

class HeroCategoryService extends BaseService implements HeroCategoryServiceInterface
{
    protected HeroCategoryRepositoryInterface $HeroCategoryRepository;

    public function __construct(HeroCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->HeroCategoryRepository = $repository;
    }

    public function getHeroCategories()
    {
        return $this->HeroCategoryRepository->getHeroCategories();
    }

    public function getAvailableCategories($heroCategories)
    {
        return $this->HeroCategoryRepository->getAvailableCategories($heroCategories);
    }

    public function getCategoriesForCreate()
    {
        return $this->HeroCategoryRepository->getCategoriesForCreate();
    }

    public function getCategoriesForEdit($heroCategory)
    {
        return $this->HeroCategoryRepository->getCategoriesForEdit($heroCategory);
    }

    public function createHeroCategory(array $data)
    {
        return $this->HeroCategoryRepository->createHeroCategory($data);
    }

    public function updateHeroCategory(\App\Models\HeroCategory $heroCategory, array $data)
    {
        return $this->HeroCategoryRepository->updateHeroCategory($heroCategory, $data);
    }

    public function deleteHeroCategory(\App\Models\HeroCategory $heroCategory)
    {
        return $this->HeroCategoryRepository->deleteHeroCategory($heroCategory);
    }

    public function reorderHeroCategories(array $items)
    {
        return $this->HeroCategoryRepository->reorderHeroCategories($items);
    }
}
