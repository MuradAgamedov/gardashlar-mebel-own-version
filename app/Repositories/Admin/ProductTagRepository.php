<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductTagRepositoryInterface;
use App\Models\ProductTag;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductTagRepository extends BaseRepository implements ProductTagRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductTag $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }

    public function getTags()
    {
        return $this->productTag->with('translates.language')->ordered()->get();
    }
}
