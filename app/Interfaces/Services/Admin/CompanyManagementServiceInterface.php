<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface CompanyManagementServiceInterface extends BaseServiceInterface
{
    public function updateCompanyManagement(array $translations, $languages);
    public function uploadImage($imageFile);
}
