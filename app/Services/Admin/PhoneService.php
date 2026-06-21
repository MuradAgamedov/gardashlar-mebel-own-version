<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\PhoneRepositoryInterface;
use App\Interfaces\Services\Admin\PhoneServiceInterface;
use App\Services\BaseService;

class PhoneService extends BaseService implements PhoneServiceInterface
{
    protected PhoneRepositoryInterface $PhoneRepository;

    public function __construct(PhoneRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->PhoneRepository = $repository;
    }

    public function getPhones()
    {
        return $this->PhoneRepository->getPhones();
    }

    public function getPhoneTypes()
    {
        return $this->PhoneRepository->getPhoneTypes();
    }

    public function getActivePhoneTypes()
    {
        return $this->PhoneRepository->getActivePhoneTypes();
    }

    public function findPhoneType($typeId)
    {
        return $this->PhoneRepository->findPhoneType($typeId);
    }

    public function createPhone(array $data)
    {
        return $this->PhoneRepository->createPhone($data);
    }

    public function updatePhone(\App\Models\Phone $phone, array $data)
    {
        return $this->PhoneRepository->updatePhone($phone, $data);
    }

    public function deletePhone(\App\Models\Phone $phone)
    {
        return $this->PhoneRepository->deletePhone($phone);
    }

    public function reorderPhones(array $phones)
    {
        return $this->PhoneRepository->reorderPhones($phones);
    }
}
