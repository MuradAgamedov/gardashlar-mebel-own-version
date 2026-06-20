<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProjectCategoryRepositoryInterface;
use App\Models\ProjectCategory;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProjectCategoryRepository extends BaseRepository implements ProjectCategoryRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProjectCategory $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
