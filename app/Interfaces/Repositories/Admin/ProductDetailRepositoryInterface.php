<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ProductDetailRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getProductDetails($product);

    public function getNextOrder($productId);

    public function createDetail($productId, $order);

    public function updateOrder($detailId, $order);

    public function getDetailsByIds($ids);
}
