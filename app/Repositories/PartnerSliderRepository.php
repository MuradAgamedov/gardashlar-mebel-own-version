<?php
namespace App\Repositories;

use App\Models\PartnerSlider;
use App\Repositories\Global\BaseRepository;

class PartnerSliderRepository extends BaseRepository
{

    public function __construct(public PartnerSlider $partnerSlider)
    {
    }

    // Bütün partner slider-ləri gətir
    public function getPartnerSliders()
    {
        return $this->partnerSlider->with('translates.language')->orderBy('order')->get();
    }
}
