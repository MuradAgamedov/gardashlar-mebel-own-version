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
}
