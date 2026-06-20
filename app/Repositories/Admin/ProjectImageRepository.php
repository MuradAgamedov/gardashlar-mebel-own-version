<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProjectImageRepositoryInterface;
use App\Models\ProjectImage;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProjectImageRepository extends BaseRepository implements ProjectImageRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProjectImage $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
