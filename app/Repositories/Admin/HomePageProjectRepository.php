<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\HomePageProjectRepositoryInterface;
use App\Models\HomePageProject;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class HomePageProjectRepository extends BaseRepository implements HomePageProjectRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(HomePageProject $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
