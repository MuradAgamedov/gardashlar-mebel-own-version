<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\MapRepositoryInterface;
use App\Models\Map;
use App\Repositories\BaseRepository;

class MapRepository extends BaseRepository implements MapRepositoryInterface
{
    public function __construct(Map $model)
    {
        parent::__construct($model);
    }
}
