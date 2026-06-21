<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ProductDetailValueRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getValuesByProductId($productId);
}
