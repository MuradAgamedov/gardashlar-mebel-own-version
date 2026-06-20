<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\VacancyRepositoryInterface;
use App\Interfaces\Services\Front\VacancyServiceInterface;
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
