<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\BlogImageRepositoryInterface;
use App\Models\BlogImage;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class BlogImageRepository extends BaseRepository implements BlogImageRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(BlogImage $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
