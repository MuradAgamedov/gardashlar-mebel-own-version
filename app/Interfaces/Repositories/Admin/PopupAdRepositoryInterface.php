<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface PopupAdRepositoryInterface extends BaseRepositoryInterface
{
    public function getPopupAds($request = null);
    public function createPopupAd(array $data, array $translations);
    public function updatePopupAd($popup, array $data, array $translations);
    public function deletePopupAd($popup);
}
