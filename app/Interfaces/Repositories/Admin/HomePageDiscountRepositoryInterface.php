<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface HomePageDiscountRepositoryInterface extends BaseRepositoryInterface
{
    public function updateHomePageDiscount(array $translations, $languages, $bgImageFile = null);
}
