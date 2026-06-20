<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\BlogCategoryRepositoryInterface;
use App\Models\BlogCategory;
use App\Repositories\BaseRepository;

class BlogCategoryRepository extends BaseRepository implements BlogCategoryRepositoryInterface
{
    public function __construct(BlogCategory $model)
    {
        parent::__construct($model);
    }
}
