<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ServiceStaticDataRepositoryInterface;
use App\Models\ServiceStaticData;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ServiceStaticDataRepository extends BaseRepository implements ServiceStaticDataRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ServiceStaticData $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
