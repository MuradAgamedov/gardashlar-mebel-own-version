<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\DashboardRepositoryInterface;
use App\Interfaces\Services\Admin\DashboardServiceInterface;
use App\Services\BaseService;

class DashboardService extends BaseService implements DashboardServiceInterface
{
    protected DashboardRepositoryInterface $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->dashboardRepository = $repository;
    }
}
