<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductPageCategoryRepositoryInterface;
use App\Models\ProductCategory;
use App\Models\ProductPageCategory;
use App\Repositories\BaseRepository;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;

class ProductPageCategoryRepository extends BaseRepository implements ProductPageCategoryRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductPageCategory $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getProductPageCategories()
    {
        return $this->productPageCategory->with('productCategory.translates.language')
            ->ordered()
            ->get();
    }

    public function getUnusedCategories($excludeIds = [])
    {
        return ProductCategory::with('translates.language')
            ->whereNotIn('id', $excludeIds)
            ->ordered()
            ->get();
    }

    public function getCategoriesNotInProductPage()
    {
        return ProductCategory::with('translates.language')
            ->whereNull('parent_id') // Sadəcə parent kategoriyalar
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('product_page_categories')
                      ->whereRaw('product_page_categories.product_category_id = product_categories.id');
            })
            ->ordered()
            ->get();
    }

    public function getCategoriesForEdit($currentCategoryId)
    {
        return ProductCategory::with('translates.language')
            ->whereNull('parent_id') // Sadəcə parent kategoriyalar
            ->where(function ($query) use ($currentCategoryId) {
                $query->where('id', $currentCategoryId)
                      ->orWhereNotExists(function ($subQuery) {
                          $subQuery->select(DB::raw(1))
                                   ->from('product_page_categories')
                                   ->whereRaw('product_page_categories.product_category_id = product_categories.id');
                      });
            })
            ->ordered()
            ->get();
    }
}
