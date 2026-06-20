<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductDetailRepositoryInterface;
use App\Interfaces\Services\Admin\ProductDetailServiceInterface;
use App\Services\BaseService;

class ProductDetailService extends BaseService implements ProductDetailServiceInterface
{
    protected ProductDetailRepositoryInterface $ProductDetailRepository;

    public function __construct(ProductDetailRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProductDetailRepository = $repository;
    }
}
