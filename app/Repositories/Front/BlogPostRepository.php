<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\BlogPostRepositoryInterface;
use App\Models\BlogPost;
use App\Repositories\BaseRepository;

class BlogPostRepository extends BaseRepository implements BlogPostRepositoryInterface
{
    public function __construct(BlogPost $model)
    {
        parent::__construct($model);
    }
}
