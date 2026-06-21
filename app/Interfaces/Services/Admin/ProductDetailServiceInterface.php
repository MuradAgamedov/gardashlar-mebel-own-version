<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ProductDetailServiceInterface extends BaseServiceInterface
{
    //

    public function getProductDetails($product);

    public function getNextOrder($productId);

    public function createDetail($productId, $order);

    public function updateOrder($detailId, $order);

    public function getDetailsByIds($ids);
}
