<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\HeroCategoryRepositoryInterface;
use App\Models\HeroCategory;
use App\Repositories\BaseRepository;

class HeroCategoryRepository extends BaseRepository implements HeroCategoryRepositoryInterface
{
    public function __construct(HeroCategory $model)
    {
        parent::__construct($model);
    }
}
