<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProjectVideoRepositoryInterface;
use App\Models\ProjectVideo;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProjectVideoRepository extends BaseRepository implements ProjectVideoRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProjectVideo $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
