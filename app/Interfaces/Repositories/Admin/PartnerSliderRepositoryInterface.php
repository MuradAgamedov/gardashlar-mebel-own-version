<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface PartnerSliderRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getPartnerSliders();

    public function createPartnerSlider(array $data, array $translations, $imageFile, $imageService, $languages);

    public function updatePartnerSlider(\App\Models\PartnerSlider $partnerSlider, array $data, array $translations, $imageFile, $imageService, $languages);

    public function deletePartnerSlider(\App\Models\PartnerSlider $partnerSlider);

    public function reorderPartnerSliders(array $sliders);
}
