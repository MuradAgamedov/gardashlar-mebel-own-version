<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface OrderServiceInterface extends BaseServiceInterface
{
    //

    public function getOrders($request = null);

    public function findOrder($id);
}
