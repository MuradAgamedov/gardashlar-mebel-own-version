<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductDetailValueRepositoryInterface;
use App\Models\ProductDetailValue;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductDetailValueRepository extends BaseRepository implements ProductDetailValueRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductDetailValue $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
