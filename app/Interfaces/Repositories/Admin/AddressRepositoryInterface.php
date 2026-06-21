<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface AddressRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getAddresses();

    public function createAddress(array $data, array $translations);

    public function updateAddress(\App\Models\Address $address, array $data, array $translations);

    public function deleteAddress(\App\Models\Address $address);

    public function reorderAddresses(array $items);
}
