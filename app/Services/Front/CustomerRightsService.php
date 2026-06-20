<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\CustomerRightsRepositoryInterface;
use App\Interfaces\Services\Front\CustomerRightsServiceInterface;
use App\Services\BaseService;

class CustomerRightsService extends BaseService implements CustomerRightsServiceInterface
{
    protected CustomerRightsRepositoryInterface $customerRightsRepository;

    public function __construct(CustomerRightsRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->customerRightsRepository = $repository;
    }
}
