<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\NewsCategoryRepositoryInterface;
use App\Models\NewsCategory;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class NewsCategoryRepository extends BaseRepository implements NewsCategoryRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(NewsCategory $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
