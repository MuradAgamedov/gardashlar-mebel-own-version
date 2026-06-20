<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\PartnerSliderRepositoryInterface;
use App\Models\PartnerSlider;
use App\Repositories\BaseRepository;

class PartnerSliderRepository extends BaseRepository implements PartnerSliderRepositoryInterface
{
    public function __construct(PartnerSlider $model)
    {
        parent::__construct($model);
    }
}
