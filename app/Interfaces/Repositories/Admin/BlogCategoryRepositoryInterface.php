<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface BlogCategoryRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getCategories();

    public function createCategory(array $data, array $translations);

    public function updateCategory(\App\Models\BlogCategory $category, array $data, array $translations);

    public function deleteCategory(\App\Models\BlogCategory $category);

    public function reorderCategories(array $categories);

    public function bulkDelete(array $ids);
}
