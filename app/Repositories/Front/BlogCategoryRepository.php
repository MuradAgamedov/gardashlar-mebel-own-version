<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\BlogCategoryRepositoryInterface;
use App\Models\BlogCategory;
use App\Repositories\BaseRepository;

class BlogCategoryRepository extends BaseRepository implements BlogCategoryRepositoryInterface
{
    public function __construct(BlogCategory $model)
    {
        parent::__construct($model);
    }
}
