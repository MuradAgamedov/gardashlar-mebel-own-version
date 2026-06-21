<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface PartnerSliderServiceInterface extends BaseServiceInterface
{
    //

    public function getPartnerSliders();

    public function createPartnerSlider(array $data, array $translations, $imageFile, $imageService, $languages);

    public function updatePartnerSlider(\App\Models\PartnerSlider $partnerSlider, array $data, array $translations, $imageFile, $imageService, $languages);

    public function deletePartnerSlider(\App\Models\PartnerSlider $partnerSlider);

    public function reorderPartnerSliders(array $sliders);
}
