<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\AddressRepositoryInterface;
use App\Interfaces\Services\Front\AddressServiceInterface;
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
