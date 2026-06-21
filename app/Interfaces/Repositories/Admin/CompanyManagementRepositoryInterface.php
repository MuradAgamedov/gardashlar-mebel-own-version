<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface CompanyManagementRepositoryInterface extends BaseRepositoryInterface
{
    public function updateCompanyManagement(array $translations, $languages);
    public function uploadImage($imageFile);
}
