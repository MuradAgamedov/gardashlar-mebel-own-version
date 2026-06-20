<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\AdministrationRepositoryInterface;
use App\Models\Administration;
use App\Repositories\BaseRepository;

class AdministrationRepository extends BaseRepository implements AdministrationRepositoryInterface
{
    public function __construct(Administration $model)
    {
        parent::__construct($model);
    }
}
