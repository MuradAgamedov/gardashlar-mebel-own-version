<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\ProductTypeRepositoryInterface;
use App\Interfaces\Services\Front\ProductTypeServiceInterface;
use App\Services\BaseService;

class ProductTypeService extends BaseService implements ProductTypeServiceInterface
{
    protected ProductTypeRepositoryInterface $productTypeRepository;

    public function __construct(ProductTypeRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->productTypeRepository = $repository;
    }
}
