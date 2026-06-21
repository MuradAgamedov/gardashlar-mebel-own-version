<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface PhoneTypeServiceInterface extends BaseServiceInterface
{
    //

    public function getPhoneTypes();

    public function createPhoneType(array $data, array $translations, $languages);

    public function updatePhoneType(\App\Models\PhoneType $phoneType, array $data, array $translations, $languages);

    public function deletePhoneType(\App\Models\PhoneType $phoneType);

    public function reorderPhoneTypes(array $phoneTypes);
}
