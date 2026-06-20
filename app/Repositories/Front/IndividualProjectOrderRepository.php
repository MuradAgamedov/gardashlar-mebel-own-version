<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\IndividualProjectOrderRepositoryInterface;
use App\Models\IndividualProjectOrder;
use App\Repositories\BaseRepository;

class IndividualProjectOrderRepository extends BaseRepository implements IndividualProjectOrderRepositoryInterface
{
    public function __construct(IndividualProjectOrder $model)
    {
        parent::__construct($model);
    }
}
