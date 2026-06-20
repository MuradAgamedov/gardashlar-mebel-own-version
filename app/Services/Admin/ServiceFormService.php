<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ServiceFormRepositoryInterface;
use App\Interfaces\Services\Admin\ServiceFormServiceInterface;
use App\Services\BaseService;

class ServiceFormService extends BaseService implements ServiceFormServiceInterface
{
    protected ServiceFormRepositoryInterface $ServiceFormRepository;

    public function __construct(ServiceFormRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ServiceFormRepository = $repository;
    }
}
