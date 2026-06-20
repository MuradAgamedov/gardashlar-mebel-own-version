<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\ServiceRepositoryInterface;
use App\Models\Service;
use App\Repositories\BaseRepository;

class ServiceRepository extends BaseRepository implements ServiceRepositoryInterface
{
    public function __construct(Service $model)
    {
        parent::__construct($model);
    }
}
