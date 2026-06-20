<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\AdministrationRepositoryInterface;
use App\Interfaces\Services\Admin\AdministrationServiceInterface;
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
