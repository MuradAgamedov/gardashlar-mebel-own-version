<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface KivCategoryRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getCategories();

    public function createKivCategory(array $data, array $translations, $languages);

    public function updateKivCategory(\App\Models\KivCategory $kivCategory, array $data, array $translations, $languages);

    public function deleteKivCategory(\App\Models\KivCategory $kivCategory);

    public function reorderKivCategories(array $categories);
}
