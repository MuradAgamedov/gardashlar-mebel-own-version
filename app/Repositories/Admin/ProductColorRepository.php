<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductColorRepositoryInterface;
use App\Models\ProductColor;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductColorRepository extends BaseRepository implements ProductColorRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductColor $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
