<?php
namespace App\Repositories;

use App\Models\HeroCategory;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use App\Repositories\Global\BaseRepository;

class HeroCategoryRepository extends BaseRepository
{

    public function __construct(public HeroCategory $heroCategory)
    {
    }

    // Hero kateqoriyaları gətir
    public function getHeroCategories()
    {
        return $this->heroCategory->with('productCategory.translates.language')
            ->ordered()
            ->get();
    }

    // Hero-da olmayan kateqoriyalar
    public function getAvailableCategories($heroCategories)
    {
        return ProductCategory::with('translates.language')
            ->whereNotIn('id', $heroCategories->pluck('product_category_id'))
            ->ordered()
            ->get();
    }

    // Yeni hero üçün mövcud kateqoriyalar
    public function getCategoriesForCreate()
    {
        return ProductCategory::with('translates.language')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('hero_categories')
                      ->whereRaw('hero_categories.product_category_id = product_categories.id');
            })
            ->ordered()
            ->get();
    }

    // Edit üçün kateqoriyalar
    public function getCategoriesForEdit($heroCategory)
    {
        return ProductCategory::with('translates.language')
            ->where(function ($query) use ($heroCategory) {
                $query->where('id', $heroCategory->product_category_id)
                      ->orWhereNotExists(function ($subQuery) {
                          $subQuery->select(DB::raw(1))
                                   ->from('hero_categories')
                                   ->whereRaw('hero_categories.product_category_id = product_categories.id');
                      });
            })
            ->ordered()
            ->get();
    }
}
