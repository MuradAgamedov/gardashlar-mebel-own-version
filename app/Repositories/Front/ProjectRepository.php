<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\ProjectRepositoryInterface;
use App\Models\Project;
use App\Repositories\BaseRepository;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    public function __construct(Project $model)
    {
        parent::__construct($model);
    }
}
