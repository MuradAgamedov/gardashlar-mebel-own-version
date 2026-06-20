<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\CustomerRightsRepositoryInterface;
use App\Models\CustomerRights;
use App\Repositories\BaseRepository;

class CustomerRightsRepository extends BaseRepository implements CustomerRightsRepositoryInterface
{
    public function __construct(CustomerRights $model)
    {
        parent::__construct($model);
    }
}
