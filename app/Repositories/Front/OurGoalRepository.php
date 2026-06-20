<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\OurGoalRepositoryInterface;
use App\Models\OurGoal;
use App\Repositories\BaseRepository;

class OurGoalRepository extends BaseRepository implements OurGoalRepositoryInterface
{
    public function __construct(OurGoal $model)
    {
        parent::__construct($model);
    }
}
