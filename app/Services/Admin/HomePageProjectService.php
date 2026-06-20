<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\HomePageProjectRepositoryInterface;
use App\Interfaces\Services\Admin\HomePageProjectServiceInterface;
use App\Services\BaseService;

class HomePageProjectService extends BaseService implements HomePageProjectServiceInterface
{
    protected HomePageProjectRepositoryInterface $HomePageProjectRepository;

    public function __construct(HomePageProjectRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->HomePageProjectRepository = $repository;
    }
}
