<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ProductDetailValueServiceInterface extends BaseServiceInterface
{
    //

    public function getValuesByProductId($productId);
}
