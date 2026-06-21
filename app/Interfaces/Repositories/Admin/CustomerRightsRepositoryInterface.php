<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface CustomerRightsRepositoryInterface extends BaseRepositoryInterface
{
    public function updateCustomerRights(array $translations, $languages);
}
