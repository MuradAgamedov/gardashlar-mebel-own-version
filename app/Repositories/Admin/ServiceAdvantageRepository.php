<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ServiceAdvantageRepositoryInterface;
use App\Models\ServiceAdvantage;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ServiceAdvantageRepository extends BaseRepository implements ServiceAdvantageRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ServiceAdvantage $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
