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
}
