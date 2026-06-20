<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\HomePageCategoryRepositoryInterface;
use App\Models\HomePageCategory;
use App\Repositories\BaseRepository;

class HomePageCategoryRepository extends BaseRepository implements HomePageCategoryRepositoryInterface
{
    public function __construct(HomePageCategory $model)
    {
        parent::__construct($model);
    }
}
