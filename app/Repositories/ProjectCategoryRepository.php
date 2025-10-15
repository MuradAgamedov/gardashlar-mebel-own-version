<?php
namespace App\Repositories;

use App\Models\ProjectCategory;
use App\Repositories\Global\BaseRepository;

class ProjectCategoryRepository extends BaseRepository
{

    public function __construct(public ProjectCategory $projectCategory)
    {
    }

    // Bütün layihə kateqoriyalarını gətir
    public function getProjectCategories()
    {
        return $this->projectCategory->with('translates.language')->ordered()->get();
    }
}
