<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\SeoPageRepositoryInterface;
use App\Models\SeoPage;
use App\Repositories\BaseRepository;

class SeoPageRepository extends BaseRepository implements SeoPageRepositoryInterface
{
    public function __construct(SeoPage $model)
    {
        parent::__construct($model);
    }
}
