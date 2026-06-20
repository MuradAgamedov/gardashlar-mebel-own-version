<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductPageCategoryRepositoryInterface;
use App\Models\ProductPageCategory;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductPageCategoryRepository extends BaseRepository implements ProductPageCategoryRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductPageCategory $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
