<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductCategoryRepositoryInterface;
use App\Models\ProductCategory;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductCategoryRepository extends BaseRepository implements ProductCategoryRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductCategory $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
