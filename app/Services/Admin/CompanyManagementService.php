<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\CompanyManagementRepositoryInterface;
use App\Interfaces\Services\Admin\CompanyManagementServiceInterface;
use App\Services\BaseService;

class CompanyManagementService extends BaseService implements CompanyManagementServiceInterface
{
    protected CompanyManagementRepositoryInterface $CompanyManagementRepository;

    public function __construct(CompanyManagementRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->CompanyManagementRepository = $repository;
    }
}
