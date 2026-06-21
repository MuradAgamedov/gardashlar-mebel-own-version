<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ProductTypeProductRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getProductsByType($productTypeId, $request);

    public function getHomePageProductIds($productTypeId);

    public function searchProductsByType($productTypeId, $search);

    public function getProductType($productTypeId);

    public function checkHomePageStatus($productTypeId, $productId);
}
