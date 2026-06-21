<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ProductPageCategoryRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getProductPageCategories();

    public function getUnusedCategories($excludeIds = []);

    public function getCategoriesNotInProductPage();

    public function getCategoriesForEdit($currentCategoryId);
}
