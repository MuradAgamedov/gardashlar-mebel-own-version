<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductPageCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\ProductPageCategoryServiceInterface;
use App\Services\BaseService;

class ProductPageCategoryService extends BaseService implements ProductPageCategoryServiceInterface
{
    protected ProductPageCategoryRepositoryInterface $ProductPageCategoryRepository;

    public function __construct(ProductPageCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProductPageCategoryRepository = $repository;
    }

    public function getProductPageCategories()
    {
        return $this->ProductPageCategoryRepository->getProductPageCategories();
    }

    public function getUnusedCategories($excludeIds = [])
    {
        return $this->ProductPageCategoryRepository->getUnusedCategories($excludeIds);
    }

    public function getCategoriesNotInProductPage()
    {
        return $this->ProductPageCategoryRepository->getCategoriesNotInProductPage();
    }

    public function getCategoriesForEdit($currentCategoryId)
    {
        return $this->ProductPageCategoryRepository->getCategoriesForEdit($currentCategoryId);
    }
}
