<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\NewsImageRepositoryInterface;
use App\Models\NewsImage;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class NewsImageRepository extends BaseRepository implements NewsImageRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(NewsImage $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
