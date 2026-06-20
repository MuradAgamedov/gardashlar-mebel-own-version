<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\VacancyFormRepositoryInterface;
use App\Interfaces\Services\Admin\VacancyFormServiceInterface;
use App\Services\BaseService;

class VacancyFormService extends BaseService implements VacancyFormServiceInterface
{
    protected VacancyFormRepositoryInterface $VacancyFormRepository;

    public function __construct(VacancyFormRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->VacancyFormRepository = $repository;
    }
}
