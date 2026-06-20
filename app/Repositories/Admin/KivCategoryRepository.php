<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\KivCategoryRepositoryInterface;
use App\Models\KivCategory;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class KivCategoryRepository extends BaseRepository implements KivCategoryRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(KivCategory $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
