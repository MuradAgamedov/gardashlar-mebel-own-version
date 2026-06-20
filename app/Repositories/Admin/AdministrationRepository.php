<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\AdministrationRepositoryInterface;
use App\Models\Administration;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class AdministrationRepository extends BaseRepository implements AdministrationRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Administration $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
