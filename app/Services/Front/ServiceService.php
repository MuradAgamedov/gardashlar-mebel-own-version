<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\ServiceRepositoryInterface;
use App\Interfaces\Services\Front\ServiceServiceInterface;
use App\Services\BaseService;

class ServiceService extends BaseService implements ServiceServiceInterface
{
    protected ServiceRepositoryInterface $serviceRepository;

    public function __construct(ServiceRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->serviceRepository = $repository;
    }
}
