<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface HomePageProjectCategoryRepositoryInterface extends BaseRepositoryInterface
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
