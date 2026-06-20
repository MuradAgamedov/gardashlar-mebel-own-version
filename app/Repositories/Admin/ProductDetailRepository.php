<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductDetailRepositoryInterface;
use App\Models\ProductDetail;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductDetailRepository extends BaseRepository implements ProductDetailRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductDetail $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
