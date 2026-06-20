<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\PartnerSliderRepositoryInterface;
use App\Models\PartnerSlider;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class PartnerSliderRepository extends BaseRepository implements PartnerSliderRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(PartnerSlider $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
