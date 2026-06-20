<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\ProductCategoryRepositoryInterface;
use App\Models\ProductCategory;
use App\Repositories\BaseRepository;

class ProductCategoryRepository extends BaseRepository implements ProductCategoryRepositoryInterface
{
    public function __construct(ProductCategory $model)
    {
        parent::__construct($model);
    }

    public function getCategoryWithChildrenIds(int $categoryId): array
    {
        $categoryIds = [$categoryId];
        $children = $this->model->newQuery()->where('parent_id', $categoryId)->pluck('id');
        if ($children->isNotEmpty()) {
            $categoryIds = array_merge($categoryIds, $children->toArray());
        }
        return $categoryIds;
    }
}
