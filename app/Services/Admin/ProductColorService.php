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

    public function getProductColors($request)
    {
        return $this->ProductColorRepository->getProductColors($request);
    }

    public function createProductColor($colorCode)
    {
        return $this->ProductColorRepository->createProductColor($colorCode);
    }

    public function updateProductColor(\App\Models\ProductColor $productColor, $colorCode)
    {
        return $this->ProductColorRepository->updateProductColor($productColor, $colorCode);
    }

    public function deleteProductColor(\App\Models\ProductColor $productColor)
    {
        return $this->ProductColorRepository->deleteProductColor($productColor);
    }

    public function bulkDeleteProductColors(array $ids)
    {
        return $this->ProductColorRepository->bulkDeleteProductColors($ids);
    }
}
