<?php
namespace App\Repositories;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Repositories\Global\BaseRepository;

class ProjectRepository extends BaseRepository
{

    public function __construct(public Project $project)
    {
    }

    // Layihələri filtrlə və gətir
    public function getProjects($request)
    {
        $query = $this->project->with(['translates.language', 'category.translates']);
        
        // Filter by category if selected
        if ($request->filled('category_id')) {
            $query->where('project_category_id', $request->category_id);
        }
        
        return $query->ordered()->get();
    }

    // Aktiv kateqoriyaları əsas dildə gətir
    public function getActiveCategoriesWithMainLanguage($mainLanguageId)
    {
        return ProjectCategory::with(['translates' => function($query) use ($mainLanguageId) {
            $query->where('language_id', $mainLanguageId);
        }])->active()->ordered()->get();
    }

    // Aktiv kateqoriyaları gətir
    public function getActiveCategories()
    {
        return ProjectCategory::with('translates')->active()->ordered()->get();
    }

    // Order-i yenilə
    public function updateOrder($id, $order)
    {
        return $this->project->where('id', $id)->update(['order' => $order]);
    }

    // Çoxlu layihələri sil
    public function bulkDelete($ids)
    {
        return $this->project->whereIn('id', $ids)->delete();
    }
}
