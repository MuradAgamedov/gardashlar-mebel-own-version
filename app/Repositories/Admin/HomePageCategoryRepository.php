<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\HomePageCategoryRepositoryInterface;
use App\Models\HomePageCategory;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class HomePageCategoryRepository extends BaseRepository implements HomePageCategoryRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(HomePageCategory $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
