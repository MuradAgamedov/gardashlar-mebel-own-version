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

    public function getCompanies()
    {
        return $this->companyRepository->getCompanies();
    }

    public function createCompany(array $data, array $translates, $request)
    {
        return $this->companyRepository->createCompany($data, $translates, $request);
    }

    public function updateCompany(\App\Models\Company $company, array $data, array $translates, $request)
    {
        return $this->companyRepository->updateCompany($company, $data, $translates, $request);
    }

    public function deleteCompany(\App\Models\Company $company)
    {
        return $this->companyRepository->deleteCompany($company);
    }

    public function reorderCompanies(array $order)
    {
        return $this->companyRepository->reorderCompanies($order);
    }

    public function addProductToCompany(\App\Models\Company $company, int $productId)
    {
        return $this->companyRepository->addProductToCompany($company, $productId);
    }

    public function removeProductFromCompany(\App\Models\Company $company, int $productId)
    {
        return $this->companyRepository->removeProductFromCompany($company, $productId);
    }
}
