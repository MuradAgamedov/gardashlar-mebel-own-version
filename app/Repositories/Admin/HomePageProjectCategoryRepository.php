<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\HomePageProjectCategoryRepositoryInterface;
use App\Models\HomePageProjectCategory;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class HomePageProjectCategoryRepository extends BaseRepository implements HomePageProjectCategoryRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(HomePageProjectCategory $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
