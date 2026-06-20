<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\VacancyRepositoryInterface;
use App\Interfaces\Services\Admin\VacancyServiceInterface;
use App\Services\BaseService;

class VacancyService extends BaseService implements VacancyServiceInterface
{
    protected VacancyRepositoryInterface $vacancyRepository;

    public function __construct(VacancyRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->vacancyRepository = $repository;
    }
}
