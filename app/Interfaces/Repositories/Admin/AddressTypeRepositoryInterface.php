<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface AddressTypeRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getAddressTypes();

    public function createAddressType(array $data, array $translations);

    public function updateAddressType(\App\Models\AddressType $addressType, array $data, array $translations);

    public function deleteAddressType(\App\Models\AddressType $addressType);

    public function reorderAddressTypes(array $items);
}
