<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\KivRepositoryInterface;
use App\Models\Kiv;
use App\Repositories\BaseRepository;

class KivRepository extends BaseRepository implements KivRepositoryInterface
{
    public function __construct(Kiv $model)
    {
        parent::__construct($model);
    }
}
