<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\ProductCategoryServiceInterface;
use App\Services\BaseService;

class ProductCategoryService extends BaseService implements ProductCategoryServiceInterface
{
    protected ProductCategoryRepositoryInterface $productCategoryRepository;

    public function __construct(ProductCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->productCategoryRepository = $repository;
    }

    public function getCategories($request)
    {
        return $this->productCategoryRepository->getCategories($request);
    }

    public function getAllCategories()
    {
        return $this->productCategoryRepository->getAllCategories();
    }

    public function getRootCategories()
    {
        return $this->productCategoryRepository->getRootCategories();
    }

    public function getRootCategoriesExcept($categoryId)
    {
        return $this->productCategoryRepository->getRootCategoriesExcept($categoryId);
    }

    public function createProductCategory(array $data, array $translates, $imageFile)
    {
        return $this->productCategoryRepository->createProductCategory($data, $translates, $imageFile);
    }

    public function updateProductCategory(\App\Models\ProductCategory $category, array $data, array $translates, $imageFile, $removeImage)
    {
        return $this->productCategoryRepository->updateProductCategory($category, $data, $translates, $imageFile, $removeImage);
    }

    public function deleteProductCategory(\App\Models\ProductCategory $category)
    {
        return $this->productCategoryRepository->deleteProductCategory($category);
    }

    public function reorderProductCategories(array $items, $type)
    {
        return $this->productCategoryRepository->reorderProductCategories($items, $type);
    }

    public function deleteSelectedCategories(array $selectedIds)
    {
        return $this->productCategoryRepository->deleteSelectedCategories($selectedIds);
    }
}
