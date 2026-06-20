<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\BlogPostRepositoryInterface;
use App\Models\BlogPost;
use App\Repositories\BaseRepository;

class BlogPostRepository extends BaseRepository implements BlogPostRepositoryInterface
{
    public function __construct(BlogPost $model)
    {
        parent::__construct($model);
    }
}
