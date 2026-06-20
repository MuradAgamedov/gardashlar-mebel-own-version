<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\MapRepositoryInterface;
use App\Models\Map;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class MapRepository extends BaseRepository implements MapRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Map $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
