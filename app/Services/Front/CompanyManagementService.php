<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\CompanyManagementRepositoryInterface;
use App\Interfaces\Services\Front\CompanyManagementServiceInterface;
use App\Services\BaseService;

class CompanyManagementService extends BaseService implements CompanyManagementServiceInterface
{
    protected CompanyManagementRepositoryInterface $companyManagementRepository;

    public function __construct(CompanyManagementRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->companyManagementRepository = $repository;
    }
}
