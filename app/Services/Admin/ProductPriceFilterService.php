<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductPriceFilterRepositoryInterface;
use App\Interfaces\Services\Admin\ProductPriceFilterServiceInterface;
use App\Services\BaseService;

class ProductPriceFilterService extends BaseService implements ProductPriceFilterServiceInterface
{
    protected ProductPriceFilterRepositoryInterface $ProductPriceFilterRepository;

    public function __construct(ProductPriceFilterRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProductPriceFilterRepository = $repository;
    }
}
