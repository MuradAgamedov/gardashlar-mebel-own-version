<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ProductColorServiceInterface extends BaseServiceInterface
{
    //

    public function getProductColors($request);

    public function createProductColor($colorCode);

    public function updateProductColor(\App\Models\ProductColor $productColor, $colorCode);

    public function deleteProductColor(\App\Models\ProductColor $productColor);

    public function bulkDeleteProductColors(array $ids);
}
