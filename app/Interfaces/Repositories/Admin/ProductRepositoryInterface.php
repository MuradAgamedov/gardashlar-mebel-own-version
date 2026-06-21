<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getProducts($request);

    public function getActiveSubcategories();

    public function getActiveTypes();

    public function getActiveTags();
}
