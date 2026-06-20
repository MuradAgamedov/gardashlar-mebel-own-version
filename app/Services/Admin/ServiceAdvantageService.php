<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ServiceAdvantageRepositoryInterface;
use App\Interfaces\Services\Admin\ServiceAdvantageServiceInterface;
use App\Services\BaseService;

class ServiceAdvantageService extends BaseService implements ServiceAdvantageServiceInterface
{
    protected ServiceAdvantageRepositoryInterface $ServiceAdvantageRepository;

    public function __construct(ServiceAdvantageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ServiceAdvantageRepository = $repository;
    }
}
