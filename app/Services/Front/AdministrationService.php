<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\AdministrationRepositoryInterface;
use App\Interfaces\Services\Front\AdministrationServiceInterface;
use App\Services\BaseService;

class AdministrationService extends BaseService implements AdministrationServiceInterface
{
    protected AdministrationRepositoryInterface $administrationRepository;

    public function __construct(AdministrationRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->administrationRepository = $repository;
    }
}
