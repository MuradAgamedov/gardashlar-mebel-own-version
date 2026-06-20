<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ServiceFormRepositoryInterface;
use App\Models\ServiceForm;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ServiceFormRepository extends BaseRepository implements ServiceFormRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ServiceForm $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
