<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\PhoneTypeRepositoryInterface;
use App\Interfaces\Services\Admin\PhoneTypeServiceInterface;
use App\Services\BaseService;

class PhoneTypeService extends BaseService implements PhoneTypeServiceInterface
{
    protected PhoneTypeRepositoryInterface $PhoneTypeRepository;

    public function __construct(PhoneTypeRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->PhoneTypeRepository = $repository;
    }

    public function getPhoneTypes()
    {
        return $this->PhoneTypeRepository->getPhoneTypes();
    }

    public function createPhoneType(array $data, array $translations, $languages)
    {
        return $this->PhoneTypeRepository->createPhoneType($data, $translations, $languages);
    }

    public function updatePhoneType(\App\Models\PhoneType $phoneType, array $data, array $translations, $languages)
    {
        return $this->PhoneTypeRepository->updatePhoneType($phoneType, $data, $translations, $languages);
    }

    public function deletePhoneType(\App\Models\PhoneType $phoneType)
    {
        return $this->PhoneTypeRepository->deletePhoneType($phoneType);
    }

    public function reorderPhoneTypes(array $phoneTypes)
    {
        return $this->PhoneTypeRepository->reorderPhoneTypes($phoneTypes);
    }
}
