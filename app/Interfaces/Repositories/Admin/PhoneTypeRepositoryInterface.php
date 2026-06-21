<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface PhoneTypeRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getPhoneTypes();

    public function createPhoneType(array $data, array $translations, $languages);

    public function updatePhoneType(\App\Models\PhoneType $phoneType, array $data, array $translations, $languages);

    public function deletePhoneType(\App\Models\PhoneType $phoneType);

    public function reorderPhoneTypes(array $phoneTypes);
}
