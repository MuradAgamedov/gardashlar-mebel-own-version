<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\CatalogRepositoryInterface;
use App\Models\Catalog;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class CatalogRepository extends BaseRepository implements CatalogRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(Catalog $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
