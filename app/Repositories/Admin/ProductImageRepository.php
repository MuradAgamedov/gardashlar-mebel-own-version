<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductImageRepositoryInterface;
use App\Models\ProductImage;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductImageRepository extends BaseRepository implements ProductImageRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductImage $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
