<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface AddressServiceInterface extends BaseServiceInterface
{
    //

    public function getAddresses();

    public function createAddress(array $data, array $translations);

    public function updateAddress(\App\Models\Address $address, array $data, array $translations);

    public function deleteAddress(\App\Models\Address $address);

    public function reorderAddresses(array $items);
}
