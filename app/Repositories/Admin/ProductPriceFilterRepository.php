<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductPriceFilterRepositoryInterface;
use App\Models\ProductPriceFilter;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductPriceFilterRepository extends BaseRepository implements ProductPriceFilterRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductPriceFilter $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
