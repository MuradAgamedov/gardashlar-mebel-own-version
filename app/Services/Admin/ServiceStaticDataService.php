<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ServiceStaticDataRepositoryInterface;
use App\Interfaces\Services\Admin\ServiceStaticDataServiceInterface;
use App\Services\BaseService;

class ServiceStaticDataService extends BaseService implements ServiceStaticDataServiceInterface
{
    protected ServiceStaticDataRepositoryInterface $ServiceStaticDataRepository;

    public function __construct(ServiceStaticDataRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ServiceStaticDataRepository = $repository;
    }
}
