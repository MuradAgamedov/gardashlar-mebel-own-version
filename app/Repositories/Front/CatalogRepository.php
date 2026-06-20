<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\CatalogRepositoryInterface;
use App\Models\Catalog;
use App\Repositories\BaseRepository;

class CatalogRepository extends BaseRepository implements CatalogRepositoryInterface
{
    public function __construct(Catalog $model)
    {
        parent::__construct($model);
    }
}
