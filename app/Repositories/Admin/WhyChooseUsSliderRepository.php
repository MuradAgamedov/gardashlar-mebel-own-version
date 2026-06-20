<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\WhyChooseUsSliderRepositoryInterface;
use App\Models\WhyChooseUsSlider;
use App\Repositories\BaseRepository;
use App\Services\ImageService;

class WhyChooseUsSliderRepository extends BaseRepository implements WhyChooseUsSliderRepositoryInterface
{
    protected ImageService $imageService;

    public function __construct(WhyChooseUsSlider $model, ImageService $imageService)
    {
        parent::__construct($model);
        $this->imageService = $imageService;
    }
}
