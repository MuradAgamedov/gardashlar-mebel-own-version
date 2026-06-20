<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\HomePageProjectCategoryRepositoryInterface;
use App\Models\HomePageProjectCategory;
use App\Repositories\BaseRepository;

class HomePageProjectCategoryRepository extends BaseRepository implements HomePageProjectCategoryRepositoryInterface
{
    public function __construct(HomePageProjectCategory $model)
    {
        parent::__construct($model);
    }
}
