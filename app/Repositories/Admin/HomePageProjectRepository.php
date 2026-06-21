<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\HomePageProjectRepositoryInterface;
use App\Models\HomePageProject;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class HomePageProjectRepository extends BaseRepository implements HomePageProjectRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(HomePageProject $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getHomePageProjects()
    {
        return $this->homePageProject->with(['project.translates.language', 'projectCategory.translates.language'])
            ->orderBy('order')
            ->get();
    }

    public function getProjectsWithMainLanguage($mainLanguage)
    {
        return Project::with(['translates' => function($query) use ($mainLanguage) {
            $query->where('language_id', $mainLanguage->id);
        }])->get();
    }

    public function getCategoriesWithMainLanguage($mainLanguage)
    {
        return ProjectCategory::with(['translates' => function($query) use ($mainLanguage) {
            $query->where('language_id', $mainLanguage->id);
        }])->get();
    }

    public function getProjectsByCategory($categoryId, $mainLanguage)
    {
        return Project::with(['translates' => function($query) use ($mainLanguage) {
            $query->where('language_id', $mainLanguage->id);
        }])
        ->where('project_category_id', $categoryId)
        ->get();
    }

    public function createHomePageProject(array $data)
    {
        $maxOrder = $this->homePageProject->max('order') ?? 0;
        
        return $this->homePageProject->create([
            'project_id' => $data['project_id'],
            'project_category_id' => $data['project_category_id'],
            'order' => $maxOrder + 1,
            'status' => $data['status'] ?? true,
        ]);
    }

    public function updateHomePageProject(HomePageProject $homePageProject, array $data)
    {
        $homePageProject->update($data);
        return $homePageProject;
    }

    public function deleteHomePageProject(HomePageProject $homePageProject)
    {
        return $homePageProject->delete();
    }

    public function reorderHomePageProjects(array $items)
    {
        foreach ($items as $index => $itemId) {
            $this->homePageProject->where('id', $itemId)->update(['order' => $index + 1]);
        }
        return true;
    }
}
