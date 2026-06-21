<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BlogCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\BlogCategoryServiceInterface;
use App\Services\BaseService;

class BlogCategoryService extends BaseService implements BlogCategoryServiceInterface
{
    protected BlogCategoryRepositoryInterface $blogCategoryRepository;

    public function __construct(BlogCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->blogCategoryRepository = $repository;
    }

    public function getCategories()
    {
        return $this->blogCategoryRepository->getCategories();
    }

    public function createCategory(array $data, array $translations)
    {
        return $this->blogCategoryRepository->createCategory($data, $translations);
    }

    public function updateCategory(\App\Models\BlogCategory $category, array $data, array $translations)
    {
        return $this->blogCategoryRepository->updateCategory($category, $data, $translations);
    }

    public function deleteCategory(\App\Models\BlogCategory $category)
    {
        return $this->blogCategoryRepository->deleteCategory($category);
    }

    public function reorderCategories(array $categories)
    {
        return $this->blogCategoryRepository->reorderCategories($categories);
    }

    public function bulkDelete(array $ids)
    {
        return $this->blogCategoryRepository->bulkDelete($ids);
    }
}
