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
}
