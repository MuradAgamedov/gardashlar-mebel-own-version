<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductRepositoryInterface;
use App\Interfaces\Services\Admin\ProductServiceInterface;
use App\Services\BaseService;

class ProductService extends BaseService implements ProductServiceInterface
{
    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->productRepository = $repository;
    }

    public function getProducts($request)
    {
        return $this->productRepository->getProducts($request);
    }

    public function getActiveSubcategories()
    {
        return $this->productRepository->getActiveSubcategories();
    }

    public function getActiveTypes()
    {
        return $this->productRepository->getActiveTypes();
    }

    public function getActiveTags()
    {
        return $this->productRepository->getActiveTags();
    }
}
