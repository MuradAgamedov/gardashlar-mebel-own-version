<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface HeroProductRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getHeroProducts();

    public function searchProducts($search, $excludeIds = []);

    public function createHeroProduct(array $data);

    public function updateHeroProduct(\App\Models\HeroProduct $heroProduct, array $data);

    public function deleteHeroProduct(\App\Models\HeroProduct $heroProduct);

    public function reorderHeroProducts(array $items);

    public function formatSearchResults($products);
}
