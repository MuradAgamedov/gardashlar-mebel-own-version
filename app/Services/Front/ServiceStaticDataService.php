<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\ServiceStaticDataRepositoryInterface;
use App\Interfaces\Services\Front\ServiceStaticDataServiceInterface;
use App\Services\BaseService;

class ServiceStaticDataService extends BaseService implements ServiceStaticDataServiceInterface
{
    protected ServiceStaticDataRepositoryInterface $ServiceStaticDataRepository;

    public function __construct(ServiceStaticDataRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $ServiceStaticDataRepository = $repository;
    }
}
