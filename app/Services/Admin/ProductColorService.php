<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductColorRepositoryInterface;
use App\Interfaces\Services\Admin\ProductColorServiceInterface;
use App\Services\BaseService;

class ProductColorService extends BaseService implements ProductColorServiceInterface
{
    protected ProductColorRepositoryInterface $ProductColorRepository;

    public function __construct(ProductColorRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProductColorRepository = $repository;
    }
}
