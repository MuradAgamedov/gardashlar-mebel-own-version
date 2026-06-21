<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\PopupAdRepositoryInterface;
use App\Interfaces\Services\Admin\PopupAdServiceInterface;
use App\Services\BaseService;

class PopupAdService extends BaseService implements PopupAdServiceInterface
{
    protected PopupAdRepositoryInterface $popupAdRepository;

    public function __construct(PopupAdRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->popupAdRepository = $repository;
    }

    public function getPopupAds($request = null)
    {
        return $this->popupAdRepository->getPopupAds($request);
    }

    public function createPopupAd(array $data, array $translations)
    {
        return $this->popupAdRepository->createPopupAd($data, $translations);
    }

    public function updatePopupAd($popup, array $data, array $translations)
    {
        return $this->popupAdRepository->updatePopupAd($popup, $data, $translations);
    }

    public function deletePopupAd($popup)
    {
        return $this->popupAdRepository->deletePopupAd($popup);
    }
}
