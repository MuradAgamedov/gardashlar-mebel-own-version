<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\NewsPostRepositoryInterface;
use App\Models\NewsPost;
use App\Repositories\BaseRepository;

class NewsPostRepository extends BaseRepository implements NewsPostRepositoryInterface
{
    public function __construct(NewsPost $model)
    {
        parent::__construct($model);
    }
}
