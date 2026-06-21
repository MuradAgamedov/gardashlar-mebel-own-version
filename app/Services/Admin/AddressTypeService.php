<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\AddressTypeRepositoryInterface;
use App\Interfaces\Services\Admin\AddressTypeServiceInterface;
use App\Services\BaseService;

class AddressTypeService extends BaseService implements AddressTypeServiceInterface
{
    protected AddressTypeRepositoryInterface $addressTypeRepository;

    public function __construct(AddressTypeRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->addressTypeRepository = $repository;
    }

    public function getAddressTypes()
    {
        return $this->addressTypeRepository->getAddressTypes();
    }

    public function createAddressType(array $data, array $translations)
    {
        return $this->addressTypeRepository->createAddressType($data, $translations);
    }

    public function updateAddressType(\App\Models\AddressType $addressType, array $data, array $translations)
    {
        return $this->addressTypeRepository->updateAddressType($addressType, $data, $translations);
    }

    public function deleteAddressType(\App\Models\AddressType $addressType)
    {
        return $this->addressTypeRepository->deleteAddressType($addressType);
    }

    public function reorderAddressTypes(array $items)
    {
        return $this->addressTypeRepository->reorderAddressTypes($items);
    }
}
