<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface KivCategoryServiceInterface extends BaseServiceInterface
{
    //

    public function getCategories();

    public function createKivCategory(array $data, array $translations, $languages);

    public function updateKivCategory(\App\Models\KivCategory $kivCategory, array $data, array $translations, $languages);

    public function deleteKivCategory(\App\Models\KivCategory $kivCategory);

    public function reorderKivCategories(array $categories);
}
