<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\ProductTypeRepositoryInterface;
use App\Models\ProductType;
use App\Repositories\BaseRepository;

class ProductTypeRepository extends BaseRepository implements ProductTypeRepositoryInterface
{
    public function __construct(ProductType $model)
    {
        parent::__construct($model);
    }
}
