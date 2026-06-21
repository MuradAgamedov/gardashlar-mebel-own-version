<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\HomePageProjectCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\HomePageProjectCategoryServiceInterface;
use App\Services\BaseService;

class HomePageProjectCategoryService extends BaseService implements HomePageProjectCategoryServiceInterface
{
    protected HomePageProjectCategoryRepositoryInterface $HomePageProjectCategoryRepository;

    public function __construct(HomePageProjectCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->HomePageProjectCategoryRepository = $repository;
    }

    public function getHomePageProjectCategories()
    {
        return $this->HomePageProjectCategoryRepository->getHomePageProjectCategories();
    }

    public function getActiveProjectCategories()
    {
        return $this->HomePageProjectCategoryRepository->getActiveProjectCategories();
    }

    public function findWithCategory($id)
    {
        return $this->HomePageProjectCategoryRepository->findWithCategory($id);
    }

    public function createHomePageProjectCategory(array $data)
    {
        return $this->HomePageProjectCategoryRepository->createHomePageProjectCategory($data);
    }

    public function updateHomePageProjectCategory($id, array $data)
    {
        return $this->HomePageProjectCategoryRepository->updateHomePageProjectCategory($id, $data);
    }

    public function deleteHomePageProjectCategory($id)
    {
        return $this->HomePageProjectCategoryRepository->deleteHomePageProjectCategory($id);
    }

    public function reorderHomePageProjectCategories(array $items)
    {
        return $this->HomePageProjectCategoryRepository->reorderHomePageProjectCategories($items);
    }
}
