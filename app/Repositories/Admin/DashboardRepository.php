<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\DashboardRepositoryInterface;
use App\Models\Product;
use App\Repositories\BaseRepository;

class DashboardRepository extends BaseRepository implements DashboardRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}
