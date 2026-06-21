<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\AddressRepositoryInterface;
use App\Interfaces\Services\Admin\AddressServiceInterface;
use App\Services\BaseService;

class AddressService extends BaseService implements AddressServiceInterface
{
    protected AddressRepositoryInterface $addressRepository;

    public function __construct(AddressRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->addressRepository = $repository;
    }

    public function getAddresses()
    {
        return $this->addressRepository->getAddresses();
    }

    public function createAddress(array $data, array $translations)
    {
        return $this->addressRepository->createAddress($data, $translations);
    }

    public function updateAddress(\App\Models\Address $address, array $data, array $translations)
    {
        return $this->addressRepository->updateAddress($address, $data, $translations);
    }

    public function deleteAddress(\App\Models\Address $address)
    {
        return $this->addressRepository->deleteAddress($address);
    }

    public function reorderAddresses(array $items)
    {
        return $this->addressRepository->reorderAddresses($items);
    }
}
