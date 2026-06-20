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
}
