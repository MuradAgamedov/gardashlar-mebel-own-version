<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\ProductAccordionRepositoryInterface;
use App\Models\ProductAccordion;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class ProductAccordionRepository extends BaseRepository implements ProductAccordionRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(ProductAccordion $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
