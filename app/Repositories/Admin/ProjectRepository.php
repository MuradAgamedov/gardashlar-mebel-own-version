<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProjectRepositoryInterface;
use App\Models\Project;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Project $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getProjects($request)
    {
        $query = $this->project->with(['translates.language', 'category.translates']);
        
        // Filter by category if selected
        if ($request->filled('category_id')) {
            $query->where('project_category_id', $request->category_id);
        }
        
        return $query->ordered()->get();
    }

    public function getActiveCategoriesWithMainLanguage($mainLanguageId)
    {
        return ProjectCategory::with(['translates' => function($query) use ($mainLanguageId) {
            $query->where('language_id', $mainLanguageId);
        }])->active()->ordered()->get();
    }

    public function getActiveCategories()
    {
        return ProjectCategory::with('translates')->active()->ordered()->get();
    }
}
