<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\VacancyRequirementRepositoryInterface;
use App\Interfaces\Services\Admin\VacancyRequirementServiceInterface;
use App\Services\BaseService;

class VacancyRequirementService extends BaseService implements VacancyRequirementServiceInterface
{
    protected VacancyRequirementRepositoryInterface $VacancyRequirementRepository;

    public function __construct(VacancyRequirementRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->VacancyRequirementRepository = $repository;
    }
}
