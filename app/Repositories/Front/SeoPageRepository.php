<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\SeoPageRepositoryInterface;
use App\Models\SeoPage;
use App\Repositories\BaseRepository;

class SeoPageRepository extends BaseRepository implements SeoPageRepositoryInterface
{
    public function __construct(SeoPage $model)
    {
        parent::__construct($model);
    }
}
