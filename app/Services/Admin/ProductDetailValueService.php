<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductDetailValueRepositoryInterface;
use App\Interfaces\Services\Admin\ProductDetailValueServiceInterface;
use App\Services\BaseService;

class ProductDetailValueService extends BaseService implements ProductDetailValueServiceInterface
{
    protected ProductDetailValueRepositoryInterface $ProductDetailValueRepository;

    public function __construct(ProductDetailValueRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProductDetailValueRepository = $repository;
    }
}
