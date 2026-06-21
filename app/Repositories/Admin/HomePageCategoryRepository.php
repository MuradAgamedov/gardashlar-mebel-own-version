<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\HomePageCategoryRepositoryInterface;
use App\Models\HomePageCategory;
use App\Models\ProductCategory;
use App\Repositories\BaseRepository;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;

class HomePageCategoryRepository extends BaseRepository implements HomePageCategoryRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(HomePageCategory $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getHomePageCategories()
    {
        return $this->homePageCategory->with('productCategory.translates.language')
            ->ordered()
            ->get();
    }

    public function getAvailableCategories($homePageCategories)
    {
        return ProductCategory::with('translates.language')
            ->whereNotIn('id', $homePageCategories->pluck('product_category_id'))
            ->ordered()
            ->get();
    }

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

    public function createHomePageCategory(array $data)
    {
        return $this->homePageCategory->create($data);
    }

    public function updateHomePageCategory(HomePageCategory $homePageCategory, array $data)
    {
        $homePageCategory->update($data);
        return $homePageCategory;
    }

    public function deleteHomePageCategory(HomePageCategory $homePageCategory)
    {
        return $homePageCategory->delete();
    }

    public function reorderHomePageCategories(array $items)
    {
        foreach ($items as $index => $id) {
            $this->homePageCategory->where('id', $id)->update(['order' => $index + 1]);
        }
        return true;
    }
}
