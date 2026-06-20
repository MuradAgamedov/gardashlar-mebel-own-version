<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\CustomerRightsRepositoryInterface;
use App\Models\CustomerRights;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class CustomerRightsRepository extends BaseRepository implements CustomerRightsRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(CustomerRights $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
