<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\HomePageDiscountRepositoryInterface;
use App\Models\HomePageDiscount;
use App\Repositories\BaseRepository;

class HomePageDiscountRepository extends BaseRepository implements HomePageDiscountRepositoryInterface
{
    public function __construct(HomePageDiscount $model)
    {
        parent::__construct($model);
    }
}
