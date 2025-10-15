<?php

namespace App\Repositories;

use App\Models\ProjectVideo;
use App\Repositories\Global\BaseRepository;

class ProjectVideoRepository extends BaseRepository
{
    public function __construct(public ProjectVideo $projectVideo)
    {
    }

    public function getVideosByProject($projectId)
    {
        return $this->projectVideo->where('project_id', $projectId)
            ->ordered()
            ->get();
    }
}
