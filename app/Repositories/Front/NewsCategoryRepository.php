<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\NewsCategoryRepositoryInterface;
use App\Models\NewsCategory;
use App\Repositories\BaseRepository;

class NewsCategoryRepository extends BaseRepository implements NewsCategoryRepositoryInterface
{
    public function __construct(NewsCategory $model)
    {
        parent::__construct($model);
    }
}
