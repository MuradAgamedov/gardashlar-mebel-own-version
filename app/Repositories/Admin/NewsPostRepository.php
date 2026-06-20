<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\NewsPostRepositoryInterface;
use App\Models\NewsPost;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class NewsPostRepository extends BaseRepository implements NewsPostRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(NewsPost $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
