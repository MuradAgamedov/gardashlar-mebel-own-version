<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\HeroProductRepositoryInterface;
use App\Models\HeroProduct;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class HeroProductRepository extends BaseRepository implements HeroProductRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(HeroProduct $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
