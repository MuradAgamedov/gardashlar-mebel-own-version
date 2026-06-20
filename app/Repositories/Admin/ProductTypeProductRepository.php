<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductTypeProductRepositoryInterface;
use App\Models\HomePageTypeProduct;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductTypeProductRepository extends BaseRepository implements ProductTypeProductRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(HomePageTypeProduct $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
