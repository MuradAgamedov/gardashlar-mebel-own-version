<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\RoluteeteActionRepositoryInterface;
use App\Models\RoluteeteAction;
use App\Repositories\BaseRepository;

class RoluteeteActionRepository extends BaseRepository implements RoluteeteActionRepositoryInterface
{
    public function __construct(RoluteeteAction $model)
    {
        parent::__construct($model);
    }
}
