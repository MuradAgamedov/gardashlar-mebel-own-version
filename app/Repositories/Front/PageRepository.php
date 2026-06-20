<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\PageRepositoryInterface;
use App\Models\Page;
use App\Repositories\BaseRepository;

class PageRepository extends BaseRepository implements PageRepositoryInterface
{
    public function __construct(Page $model)
    {
        parent::__construct($model);
    }
}
