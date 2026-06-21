<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\HomePageCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\HomePageCategoryServiceInterface;
use App\Services\BaseService;

class HomePageCategoryService extends BaseService implements HomePageCategoryServiceInterface
{
    protected HomePageCategoryRepositoryInterface $HomePageCategoryRepository;

    public function __construct(HomePageCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->HomePageCategoryRepository = $repository;
    }

    public function getHomePageCategories()
    {
        return $this->HomePageCategoryRepository->getHomePageCategories();
    }

    public function getAvailableCategories($homePageCategories)
    {
        return $this->HomePageCategoryRepository->getAvailableCategories($homePageCategories);
    }

    public function getCategoriesForCreate()
    {
        return $this->HomePageCategoryRepository->getCategoriesForCreate();
    }

    public function getCategoriesForEdit($homePageCategory)
    {
        return $this->HomePageCategoryRepository->getCategoriesForEdit($homePageCategory);
    }

    public function createHomePageCategory(array $data)
    {
        return $this->HomePageCategoryRepository->createHomePageCategory($data);
    }

    public function updateHomePageCategory(\App\Models\HomePageCategory $homePageCategory, array $data)
    {
        return $this->HomePageCategoryRepository->updateHomePageCategory($homePageCategory, $data);
    }

    public function deleteHomePageCategory(\App\Models\HomePageCategory $homePageCategory)
    {
        return $this->HomePageCategoryRepository->deleteHomePageCategory($homePageCategory);
    }

    public function reorderHomePageCategories(array $items)
    {
        return $this->HomePageCategoryRepository->reorderHomePageCategories($items);
    }
}
