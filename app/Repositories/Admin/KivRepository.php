<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\KivRepositoryInterface;
use App\Models\Kiv;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class KivRepository extends BaseRepository implements KivRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Kiv $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
