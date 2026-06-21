<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\CustomerRightsRepositoryInterface;
use App\Interfaces\Services\Admin\CustomerRightsServiceInterface;
use App\Services\BaseService;

class CustomerRightsService extends BaseService implements CustomerRightsServiceInterface
{
    protected CustomerRightsRepositoryInterface $customerRightsRepository;

    public function __construct(CustomerRightsRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->customerRightsRepository = $repository;
    }

    public function updateCustomerRights(array $translations, $languages)
    {
        return $this->customerRightsRepository->updateCustomerRights($translations, $languages);
    }
}
