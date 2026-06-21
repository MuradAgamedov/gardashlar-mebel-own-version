<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\NewsCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\NewsCategoryServiceInterface;
use App\Services\BaseService;

class NewsCategoryService extends BaseService implements NewsCategoryServiceInterface
{
    protected NewsCategoryRepositoryInterface $NewsCategoryRepository;

    public function __construct(NewsCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->NewsCategoryRepository = $repository;
    }

    public function getCategories()
    {
        return $this->NewsCategoryRepository->getCategories();
    }

    public function createCategory(array $data, array $translations)
    {
        return $this->NewsCategoryRepository->createCategory($data, $translations);
    }

    public function updateCategory(\App\Models\NewsCategory $category, array $data, array $translations)
    {
        return $this->NewsCategoryRepository->updateCategory($category, $data, $translations);
    }

    public function deleteCategory(\App\Models\NewsCategory $category)
    {
        return $this->NewsCategoryRepository->deleteCategory($category);
    }

    public function reorderCategories(array $categories)
    {
        return $this->NewsCategoryRepository->reorderCategories($categories);
    }

    public function bulkDelete(array $ids)
    {
        return $this->NewsCategoryRepository->bulkDelete($ids);
    }
}
