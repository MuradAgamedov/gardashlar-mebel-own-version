<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\ProductCategoryRepositoryInterface;
use App\Interfaces\Services\Front\ProductCategoryServiceInterface;
use App\Services\BaseService;

class ProductCategoryService extends BaseService implements ProductCategoryServiceInterface
{
    protected ProductCategoryRepositoryInterface $productCategoryRepository;

    public function __construct(ProductCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->productCategoryRepository = $repository;
    }

    public function getCategoryWithChildrenIds(int $categoryId): array
    {
        return $this->productCategoryRepository->getCategoryWithChildrenIds($categoryId);
    }
}
