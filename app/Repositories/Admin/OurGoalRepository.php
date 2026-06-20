<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\OurGoalRepositoryInterface;
use App\Models\OurGoal;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class OurGoalRepository extends BaseRepository implements OurGoalRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(OurGoal $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
