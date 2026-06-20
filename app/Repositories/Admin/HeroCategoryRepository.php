<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\HeroCategoryRepositoryInterface;
use App\Models\HeroCategory;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class HeroCategoryRepository extends BaseRepository implements HeroCategoryRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(HeroCategory $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
