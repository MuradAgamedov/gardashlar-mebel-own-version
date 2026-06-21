<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface HomePageProjectCategoryServiceInterface extends BaseServiceInterface
{
    //

    public function getHomePageProjectCategories();

    public function getActiveProjectCategories();

    public function findWithCategory($id);

    public function createHomePageProjectCategory(array $data);

    public function updateHomePageProjectCategory($id, array $data);

    public function deleteHomePageProjectCategory($id);

    public function reorderHomePageProjectCategories(array $items);
}
