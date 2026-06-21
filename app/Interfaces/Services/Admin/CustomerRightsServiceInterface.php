<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface CustomerRightsServiceInterface extends BaseServiceInterface
{
    public function updateCustomerRights(array $translations, $languages);
}
