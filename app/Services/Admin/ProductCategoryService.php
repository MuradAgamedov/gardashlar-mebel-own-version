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
}
