<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\CompanyRepositoryInterface;
use App\Interfaces\Services\Front\CompanyServiceInterface;
use App\Services\BaseService;
use App\Models\Company;

class CompanyService extends BaseService implements CompanyServiceInterface
{
    protected CompanyRepositoryInterface $companyRepository;

    public function __construct(CompanyRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->companyRepository = $repository;
    }

    public function getActiveBySlug(string $slug): ?Company
    {
        return $this->companyRepository->getActiveBySlug($slug);
    }
}
