<?php

namespace App\Repositories;

use App\Models\ProductDetailValue;
use App\Repositories\Global\BaseRepository;

class ProductDetailValueRepository extends BaseRepository
{
    public function getValuesByProductId($productId)
    {
        return ProductDetailValue::with(['translates.language'])
            ->where('product_id', $productId)
            ->ordered()
            ->get();
    }
}
