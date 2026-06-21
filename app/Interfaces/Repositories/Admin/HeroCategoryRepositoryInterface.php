<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface HeroCategoryRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getHeroCategories();

    public function getAvailableCategories($heroCategories);

    public function getCategoriesForCreate();

    public function getCategoriesForEdit($heroCategory);

    public function createHeroCategory(array $data);

    public function updateHeroCategory(\App\Models\HeroCategory $heroCategory, array $data);

    public function deleteHeroCategory(\App\Models\HeroCategory $heroCategory);

    public function reorderHeroCategories(array $items);
}
