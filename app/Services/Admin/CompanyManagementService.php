<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\CompanyManagementRepositoryInterface;
use App\Interfaces\Services\Admin\CompanyManagementServiceInterface;
use App\Services\BaseService;

class CompanyManagementService extends BaseService implements CompanyManagementServiceInterface
{
    protected CompanyManagementRepositoryInterface $companyManagementRepository;

    public function __construct(CompanyManagementRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->companyManagementRepository = $repository;
    }

    public function updateCompanyManagement(array $translations, $languages)
    {
        return $this->companyManagementRepository->updateCompanyManagement($translations, $languages);
    }

    public function uploadImage($imageFile)
    {
        return $this->companyManagementRepository->uploadImage($imageFile);
    }
}
