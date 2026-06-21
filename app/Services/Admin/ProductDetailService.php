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

    public function getProductDetails($product)
    {
        return $this->ProductDetailRepository->getProductDetails($product);
    }

    public function getNextOrder($productId)
    {
        return $this->ProductDetailRepository->getNextOrder($productId);
    }

    public function createDetail($productId, $order)
    {
        return $this->ProductDetailRepository->createDetail($productId, $order);
    }

    public function updateOrder($detailId, $order)
    {
        return $this->ProductDetailRepository->updateOrder($detailId, $order);
    }

    public function getDetailsByIds($ids)
    {
        return $this->ProductDetailRepository->getDetailsByIds($ids);
    }
}
