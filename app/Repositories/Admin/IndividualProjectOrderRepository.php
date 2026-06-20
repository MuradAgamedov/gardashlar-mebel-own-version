<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\IndividualProjectOrderRepositoryInterface;
use App\Models\IndividualProjectOrder;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class IndividualProjectOrderRepository extends BaseRepository implements IndividualProjectOrderRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(IndividualProjectOrder $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
