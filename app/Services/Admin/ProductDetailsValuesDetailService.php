<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductDetailsValuesDetailRepositoryInterface;
use App\Interfaces\Services\Admin\ProductDetailsValuesDetailServiceInterface;
use App\Services\BaseService;

class ProductDetailsValuesDetailService extends BaseService implements ProductDetailsValuesDetailServiceInterface
{
    protected ProductDetailsValuesDetailRepositoryInterface $ProductDetailsValuesDetailRepository;

    public function __construct(ProductDetailsValuesDetailRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProductDetailsValuesDetailRepository = $repository;
    }

    public function getDetailsByValueId($detailValueId)
    {
        return $this->ProductDetailsValuesDetailRepository->getDetailsByValueId($detailValueId);
    }
}
