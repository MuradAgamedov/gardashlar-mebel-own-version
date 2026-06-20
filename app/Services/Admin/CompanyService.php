<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\CompanyRepositoryInterface;
use App\Interfaces\Services\Admin\CompanyServiceInterface;
use App\Services\BaseService;

class CompanyService extends BaseService implements CompanyServiceInterface
{
    protected CompanyRepositoryInterface $companyRepository;

    public function __construct(CompanyRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->companyRepository = $repository;
    }
}
