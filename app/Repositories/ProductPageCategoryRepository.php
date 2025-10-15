<?php
namespace App\Repositories;

use App\Models\ProductPageCategory;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use App\Repositories\Global\BaseRepository;

class ProductPageCategoryRepository extends BaseRepository
{

    public function __construct(public ProductPageCategory $productPageCategory)
    {
    }

    // Bütün səhifə kateqoriyalarını gətir
    public function getProductPageCategories()
    {
        return $this->productPageCategory->with('productCategory.translates.language')
            ->ordered()
            ->get();
    }

    // İstifadə olunmayan kateqoriyaları gətir
    public function getUnusedCategories($excludeIds = [])
    {
        return ProductCategory::with('translates.language')
            ->whereNotIn('id', $excludeIds)
            ->ordered()
            ->get();
    }

    // Səhifə kateqoriyasında olmayan kateqoriyaları gətir
    public function getCategoriesNotInProductPage()
    {
        return ProductCategory::with('translates.language')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('product_page_categories')
                      ->whereRaw('product_page_categories.product_category_id = product_categories.id');
            })
            ->ordered()
            ->get();
    }

    // Edit üçün kateqoriyaları gətir (cari və istifadə olunmayanlar)
    public function getCategoriesForEdit($currentCategoryId)
    {
        return ProductCategory::with('translates.language')
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
