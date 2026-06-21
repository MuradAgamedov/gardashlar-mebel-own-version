<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface HomePageDiscountServiceInterface extends BaseServiceInterface
{
    public function updateHomePageDiscount(array $translations, $languages, $bgImageFile = null);
}
