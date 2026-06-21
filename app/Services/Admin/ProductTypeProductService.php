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

    public function getProductsByType($productTypeId, $request)
    {
        return $this->ProductTypeProductRepository->getProductsByType($productTypeId, $request);
    }

    public function getHomePageProductIds($productTypeId)
    {
        return $this->ProductTypeProductRepository->getHomePageProductIds($productTypeId);
    }

    public function searchProductsByType($productTypeId, $search)
    {
        return $this->ProductTypeProductRepository->searchProductsByType($productTypeId, $search);
    }

    public function getProductType($productTypeId)
    {
        return $this->ProductTypeProductRepository->getProductType($productTypeId);
    }

    public function checkHomePageStatus($productTypeId, $productId)
    {
        return $this->ProductTypeProductRepository->checkHomePageStatus($productTypeId, $productId);
    }
}
