<?php

namespace App\Interfaces\Repositories\Front;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ProductCategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function getCategoryWithChildrenIds(int $categoryId): array;
}
