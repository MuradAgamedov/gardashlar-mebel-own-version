<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ProductPageCategoryServiceInterface extends BaseServiceInterface
{
    //

    public function getProductPageCategories();

    public function getUnusedCategories($excludeIds = []);

    public function getCategoriesNotInProductPage();

    public function getCategoriesForEdit($currentCategoryId);
}
