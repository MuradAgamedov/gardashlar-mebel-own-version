<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductRepositoryInterface;
use App\Models\Product;
use App\Repositories\BaseRepository;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}
