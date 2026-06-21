<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface AddressTypeServiceInterface extends BaseServiceInterface
{
    //

    public function getAddressTypes();

    public function createAddressType(array $data, array $translations);

    public function updateAddressType(\App\Models\AddressType $addressType, array $data, array $translations);

    public function deleteAddressType(\App\Models\AddressType $addressType);

    public function reorderAddressTypes(array $items);
}
