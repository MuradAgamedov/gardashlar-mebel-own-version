<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductImageRepositoryInterface;
use App\Interfaces\Services\Admin\ProductImageServiceInterface;
use App\Services\BaseService;

class ProductImageService extends BaseService implements ProductImageServiceInterface
{
    protected ProductImageRepositoryInterface $productImageRepository;

    public function __construct(ProductImageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->productImageRepository = $repository;
    }

    public function getProductImages($product, $request)
    {
        return $this->productImageRepository->getProductImages($product, $request);
    }

    public function getAllColors()
    {
        return $this->productImageRepository->getAllColors();
    }
}
