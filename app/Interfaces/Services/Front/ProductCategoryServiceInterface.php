<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;

interface ProductCategoryServiceInterface extends BaseServiceInterface
{
    public function getCategoryWithChildrenIds(int $categoryId): array;
}
