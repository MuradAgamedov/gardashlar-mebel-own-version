<?php

namespace App\Repositories;

use App\Models\ProductDetailsValuesDetail;
use App\Repositories\Global\BaseRepository;

class ProductDetailsValuesDetailRepository extends BaseRepository
{
    public function getDetailsByValueId($detailValueId)
    {
        return ProductDetailsValuesDetail::with(['translates.language'])
            ->where('product_detail_value_id', $detailValueId)
            ->ordered()
            ->get();
    }
}
