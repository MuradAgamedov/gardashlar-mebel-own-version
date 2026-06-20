<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductRepositoryInterface;
use App\Interfaces\Services\Admin\ProductServiceInterface;
use App\Services\BaseService;

class ProductService extends BaseService implements ProductServiceInterface
{
    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->productRepository = $repository;
    }
}
