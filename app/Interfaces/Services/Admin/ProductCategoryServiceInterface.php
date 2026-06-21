<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ProductCategoryServiceInterface extends BaseServiceInterface
{
    //

    public function getCategories($request);

    public function getAllCategories();

    public function getRootCategories();

    public function getRootCategoriesExcept($categoryId);

    public function createProductCategory(array $data, array $translates, $imageFile);

    public function updateProductCategory(\App\Models\ProductCategory $category, array $data, array $translates, $imageFile, $removeImage);

    public function deleteProductCategory(\App\Models\ProductCategory $category);

    public function reorderProductCategories(array $items, $type);

    public function deleteSelectedCategories(array $selectedIds);
}
