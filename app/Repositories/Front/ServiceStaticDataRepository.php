<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\ServiceStaticDataRepositoryInterface;
use App\Models\ServiceStaticData;
use App\Repositories\BaseRepository;

class ServiceStaticDataRepository extends BaseRepository implements ServiceStaticDataRepositoryInterface
{
    public function __construct(ServiceStaticData $model)
    {
        parent::__construct($model);
    }
}
