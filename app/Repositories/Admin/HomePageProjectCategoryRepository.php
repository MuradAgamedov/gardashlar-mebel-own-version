<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\HomePageProjectCategoryRepositoryInterface;
use App\Models\HomePageProjectCategory;
use App\Models\ProjectCategory;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class HomePageProjectCategoryRepository extends BaseRepository implements HomePageProjectCategoryRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(HomePageProjectCategory $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getHomePageProjectCategories()
    {
        return $this->homePageProjectCategory->with('projectCategory.translates')
            ->ordered()
            ->get();
    }

    public function getActiveProjectCategories()
    {
        return ProjectCategory::with('translates')
            ->active()
            ->ordered()
            ->get();
    }

    public function findWithCategory($id)
    {
        return $this->homePageProjectCategory->with('projectCategory.translates')->findOrFail($id);
    }

    public function createHomePageProjectCategory(array $data)
    {
        return $this->homePageProjectCategory->create($data);
    }

    public function updateHomePageProjectCategory($id, array $data)
    {
        $homePageProjectCategory = $this->homePageProjectCategory->findOrFail($id);
        $homePageProjectCategory->update($data);
        return $homePageProjectCategory;
    }

    public function deleteHomePageProjectCategory($id)
    {
        $homePageProjectCategory = $this->homePageProjectCategory->findOrFail($id);
        return $homePageProjectCategory->delete();
    }

    public function reorderHomePageProjectCategories(array $items)
    {
        foreach ($items as $item) {
            $this->homePageProjectCategory->where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return true;
    }
}
