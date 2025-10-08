<?php
namespace App\Repositories;

use App\Models\HomePageProjectCategory;
use App\Models\ProjectCategory;
use App\Repositories\Global\BaseRepository;

class HomePageProjectCategoryRepository extends BaseRepository
{

    public function __construct(public HomePageProjectCategory $homePageProjectCategory)
    {
    }

    // Ana səhifə layihə kateqoriyaları
    public function getHomePageProjectCategories()
    {
        return $this->homePageProjectCategory->with('projectCategory.translates')
            ->ordered()
            ->get();
    }

    // Aktiv layihə kateqoriyaları
    public function getActiveProjectCategories()
    {
        return ProjectCategory::with('translates')
            ->active()
            ->ordered()
            ->get();
    }
}
