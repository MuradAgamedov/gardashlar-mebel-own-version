<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductTypeRepositoryInterface;
use App\Models\ProductType;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductTypeRepository extends BaseRepository implements ProductTypeRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductType $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
