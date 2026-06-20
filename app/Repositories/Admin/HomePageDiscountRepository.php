<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\HomePageDiscountRepositoryInterface;
use App\Models\HomePageDiscount;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class HomePageDiscountRepository extends BaseRepository implements HomePageDiscountRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(HomePageDiscount $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
