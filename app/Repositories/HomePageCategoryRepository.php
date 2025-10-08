<?php
namespace App\Repositories;

use App\Models\HomePageCategory;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use App\Repositories\Global\BaseRepository;

class HomePageCategoryRepository extends BaseRepository
{

    public function __construct(public HomePageCategory $homePageCategory)
    {
    }

    // Ana səhifə kateqoriyaları
    public function getHomePageCategories()
    {
        return $this->homePageCategory->with('productCategory.translates.language')
            ->ordered()
            ->get();
    }

    // Ana səhifədə olmayan kateqoriyalar
    public function getAvailableCategories($homePageCategories)
    {
        return ProductCategory::with('translates.language')
            ->whereNotIn('id', $homePageCategories->pluck('product_category_id'))
            ->ordered()
            ->get();
    }

    // Yeni üçün mövcud kateqoriyalar
    public function getCategoriesForCreate()
    {
        return ProductCategory::with('translates.language')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('home_page_categories')
                      ->whereRaw('home_page_categories.product_category_id = product_categories.id');
            })
            ->ordered()
            ->get();
    }

    // Edit üçün kateqoriyalar
    public function getCategoriesForEdit($homePageCategory)
    {
        return ProductCategory::with('translates.language')
            ->where(function ($query) use ($homePageCategory) {
                $query->where('id', $homePageCategory->product_category_id)
                      ->orWhereNotExists(function ($subQuery) {
                          $subQuery->select(DB::raw(1))
                                   ->from('home_page_categories')
                                   ->whereRaw('home_page_categories.product_category_id = product_categories.id');
                      });
            })
            ->ordered()
            ->get();
    }
}
