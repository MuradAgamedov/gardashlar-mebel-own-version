<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductTypeProductRepositoryInterface;
use App\Interfaces\Services\Admin\ProductTypeProductServiceInterface;
use App\Services\BaseService;

class ProductTypeProductService extends BaseService implements ProductTypeProductServiceInterface
{
    protected ProductTypeProductRepositoryInterface $ProductTypeProductRepository;

    public function __construct(ProductTypeProductRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProductTypeProductRepository = $repository;
    }
}
