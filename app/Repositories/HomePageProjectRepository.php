<?php
namespace App\Repositories;

use App\Models\HomePageProject;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Repositories\Global\BaseRepository;

class HomePageProjectRepository extends BaseRepository
{

    public function __construct(public HomePageProject $homePageProject)
    {
    }

    // Ana səhifə layihələri
    public function getHomePageProjects()
    {
        return $this->homePageProject->with(['project.translates.language', 'projectCategory.translates.language'])
            ->orderBy('order')
            ->get();
    }

    // Layihələri əsas dildə gətir
    public function getProjectsWithMainLanguage($mainLanguage)
    {
        return Project::with(['translates' => function($query) use ($mainLanguage) {
            $query->where('language_id', $mainLanguage->id);
        }])->get();
    }

    // Kateqoriyaları əsas dildə gətir
    public function getCategoriesWithMainLanguage($mainLanguage)
    {
        return ProjectCategory::with(['translates' => function($query) use ($mainLanguage) {
            $query->where('language_id', $mainLanguage->id);
        }])->get();
    }

    // Kateqoriyaya görə layihələr
    public function getProjectsByCategory($categoryId, $mainLanguage)
    {
        return Project::with(['translates' => function($query) use ($mainLanguage) {
            $query->where('language_id', $mainLanguage->id);
        }])
        ->where('project_category_id', $categoryId)
        ->get();
    }
}
