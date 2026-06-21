<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ProductColorRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getProductColors($request);

    public function createProductColor($colorCode);

    public function updateProductColor(\App\Models\ProductColor $productColor, $colorCode);

    public function deleteProductColor(\App\Models\ProductColor $productColor);

    public function bulkDeleteProductColors(array $ids);
}
