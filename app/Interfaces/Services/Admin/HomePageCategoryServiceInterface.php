<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface HomePageCategoryServiceInterface extends BaseServiceInterface
{
    //

    public function getHomePageCategories();

    public function getAvailableCategories($homePageCategories);

    public function getCategoriesForCreate();

    public function getCategoriesForEdit($homePageCategory);

    public function createHomePageCategory(array $data);

    public function updateHomePageCategory(\App\Models\HomePageCategory $homePageCategory, array $data);

    public function deleteHomePageCategory(\App\Models\HomePageCategory $homePageCategory);

    public function reorderHomePageCategories(array $items);
}
