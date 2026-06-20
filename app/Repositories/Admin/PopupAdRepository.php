<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\PopupAdRepositoryInterface;
use App\Models\PopupAd;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class PopupAdRepository extends BaseRepository implements PopupAdRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(PopupAd $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
