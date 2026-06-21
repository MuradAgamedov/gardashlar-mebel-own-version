<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\PartnerSliderRepositoryInterface;
use App\Interfaces\Services\Admin\PartnerSliderServiceInterface;
use App\Services\BaseService;

class PartnerSliderService extends BaseService implements PartnerSliderServiceInterface
{
    protected PartnerSliderRepositoryInterface $PartnerSliderRepository;

    public function __construct(PartnerSliderRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->PartnerSliderRepository = $repository;
    }

    public function getPartnerSliders()
    {
        return $this->PartnerSliderRepository->getPartnerSliders();
    }

    public function createPartnerSlider(array $data, array $translations, $imageFile, $imageService, $languages)
    {
        return $this->PartnerSliderRepository->createPartnerSlider($data, $translations, $imageFile, $imageService, $languages);
    }

    public function updatePartnerSlider(\App\Models\PartnerSlider $partnerSlider, array $data, array $translations, $imageFile, $imageService, $languages)
    {
        return $this->PartnerSliderRepository->updatePartnerSlider($partnerSlider, $data, $translations, $imageFile, $imageService, $languages);
    }

    public function deletePartnerSlider(\App\Models\PartnerSlider $partnerSlider)
    {
        return $this->PartnerSliderRepository->deletePartnerSlider($partnerSlider);
    }

    public function reorderPartnerSliders(array $sliders)
    {
        return $this->PartnerSliderRepository->reorderPartnerSliders($sliders);
    }
}
