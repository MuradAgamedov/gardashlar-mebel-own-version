<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface PopupAdServiceInterface extends BaseServiceInterface
{
    public function getPopupAds($request = null);
    public function createPopupAd(array $data, array $translations);
    public function updatePopupAd($popup, array $data, array $translations);
    public function deletePopupAd($popup);
}
