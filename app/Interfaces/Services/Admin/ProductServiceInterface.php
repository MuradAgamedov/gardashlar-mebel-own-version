<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ProductServiceInterface extends BaseServiceInterface
{
    //

    public function getProducts($request);

    public function getActiveSubcategories();

    public function getActiveTypes();

    public function getActiveTags();
}
