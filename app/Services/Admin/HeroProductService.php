<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\HeroProductRepositoryInterface;
use App\Interfaces\Services\Admin\HeroProductServiceInterface;
use App\Services\BaseService;

class HeroProductService extends BaseService implements HeroProductServiceInterface
{
    protected HeroProductRepositoryInterface $HeroProductRepository;

    public function __construct(HeroProductRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->HeroProductRepository = $repository;
    }

    public function getHeroProducts()
    {
        return $this->HeroProductRepository->getHeroProducts();
    }

    public function searchProducts($search, $excludeIds = [])
    {
        return $this->HeroProductRepository->searchProducts($search, $excludeIds);
    }

    public function createHeroProduct(array $data)
    {
        return $this->HeroProductRepository->createHeroProduct($data);
    }

    public function updateHeroProduct(\App\Models\HeroProduct $heroProduct, array $data)
    {
        return $this->HeroProductRepository->updateHeroProduct($heroProduct, $data);
    }

    public function deleteHeroProduct(\App\Models\HeroProduct $heroProduct)
    {
        return $this->HeroProductRepository->deleteHeroProduct($heroProduct);
    }

    public function reorderHeroProducts(array $items)
    {
        return $this->HeroProductRepository->reorderHeroProducts($items);
    }

    public function formatSearchResults($products)
    {
        return $this->HeroProductRepository->formatSearchResults($products);
    }
}
