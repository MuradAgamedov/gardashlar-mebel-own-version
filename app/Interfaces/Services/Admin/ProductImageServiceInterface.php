<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ProductImageServiceInterface extends BaseServiceInterface
{
    //

    public function getProductImages($product, $request);

    public function getAllColors();
}
