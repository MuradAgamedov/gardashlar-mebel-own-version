<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProjectVideoRepositoryInterface;
use App\Models\ProjectVideo;
use App\Repositories\BaseRepository;

class ProjectVideoRepository extends BaseRepository implements ProjectVideoRepositoryInterface
{
    public function __construct(ProjectVideo $model)
    {
        parent::__construct($model);
    }

    public function getVideosByProject($projectId)
    {
        return $this->model->newQuery()
            ->where('project_id', $projectId)
            ->orderBy('order')
            ->get();
    }
}
