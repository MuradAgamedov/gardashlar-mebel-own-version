<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface PhoneServiceInterface extends BaseServiceInterface
{
    //

    public function getPhones();

    public function getPhoneTypes();

    public function getActivePhoneTypes();

    public function findPhoneType($typeId);

    public function createPhone(array $data);

    public function updatePhone(\App\Models\Phone $phone, array $data);

    public function deletePhone(\App\Models\Phone $phone);

    public function reorderPhones(array $phones);
}
