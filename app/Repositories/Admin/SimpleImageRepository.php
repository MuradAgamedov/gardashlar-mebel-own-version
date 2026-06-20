<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\SimpleImageRepositoryInterface;
use App\Models\SimpleImage;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class SimpleImageRepository extends BaseRepository implements SimpleImageRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(SimpleImage $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
