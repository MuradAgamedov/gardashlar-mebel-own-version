<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\VacancyFormRepositoryInterface;
use App\Interfaces\Services\Front\VacancyFormServiceInterface;
use App\Services\BaseService;

class VacancyFormService extends BaseService implements VacancyFormServiceInterface
{
    protected VacancyFormRepositoryInterface $vacancyFormRepository;

    public function __construct(VacancyFormRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->vacancyFormRepository = $repository;
    }
}
