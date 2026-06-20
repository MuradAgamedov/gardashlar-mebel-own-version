<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\ProjectCategoryRepositoryInterface;
use App\Models\ProjectCategory;
use App\Repositories\BaseRepository;

class ProjectCategoryRepository extends BaseRepository implements ProjectCategoryRepositoryInterface
{
    public function __construct(ProjectCategory $model)
    {
        parent::__construct($model);
    }
}
