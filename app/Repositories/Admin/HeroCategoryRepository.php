<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\HeroCategoryRepositoryInterface;
use App\Models\HeroCategory;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class HeroCategoryRepository extends BaseRepository implements HeroCategoryRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(HeroCategory $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getHeroCategories()
    {
        return $this->heroCategory->with('productCategory.translates.language')
            ->ordered()
            ->get();
    }

    public function getAvailableCategories($heroCategories)
    {
        return ProductCategory::with('translates.language')
            ->whereNotIn('id', $heroCategories->pluck('product_category_id'))
            ->ordered()
            ->get();
    }

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

    public function createHeroCategory(array $data)
    {
        return $this->heroCategory->create($data);
    }

    public function updateHeroCategory(HeroCategory $heroCategory, array $data)
    {
        $heroCategory->update($data);
        return $heroCategory;
    }

    public function deleteHeroCategory(HeroCategory $heroCategory)
    {
        return $heroCategory->delete();
    }

    public function reorderHeroCategories(array $items)
    {
        foreach ($items as $index => $id) {
            $this->heroCategory->where('id', $id)->update(['order' => $index + 1]);
        }
        return true;
    }
}
