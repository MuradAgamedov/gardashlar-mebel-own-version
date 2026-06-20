<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\CustomerRightsRepositoryInterface;
use App\Interfaces\Services\Admin\CustomerRightsServiceInterface;
use App\Services\BaseService;

class CustomerRightsService extends BaseService implements CustomerRightsServiceInterface
{
    protected CustomerRightsRepositoryInterface $CustomerRightsRepository;

    public function __construct(CustomerRightsRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->CustomerRightsRepository = $repository;
    }
}
