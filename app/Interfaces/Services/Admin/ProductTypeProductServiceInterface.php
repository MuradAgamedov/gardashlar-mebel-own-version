<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ProductTypeProductServiceInterface extends BaseServiceInterface
{
    //

    public function getProductsByType($productTypeId, $request);

    public function getHomePageProductIds($productTypeId);

    public function searchProductsByType($productTypeId, $search);

    public function getProductType($productTypeId);

    public function checkHomePageStatus($productTypeId, $productId);
}
