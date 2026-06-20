<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\WhyChooseUsSliderRepositoryInterface;
use App\Models\WhyChooseUsSlider;
use App\Repositories\BaseRepository;

class WhyChooseUsSliderRepository extends BaseRepository implements WhyChooseUsSliderRepositoryInterface
{
    public function __construct(WhyChooseUsSlider $model)
    {
        parent::__construct($model);
    }
}
