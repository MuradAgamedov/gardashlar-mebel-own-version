<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductTypeRepositoryInterface;
use App\Interfaces\Services\Admin\ProductTypeServiceInterface;
use App\Services\BaseService;

class ProductTypeService extends BaseService implements ProductTypeServiceInterface
{
    protected ProductTypeRepositoryInterface $ProductTypeRepository;

    public function __construct(ProductTypeRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProductTypeRepository = $repository;
    }
}
