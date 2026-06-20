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
}
