<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\OurGoalRepositoryInterface;
use App\Interfaces\Services\Admin\OurGoalServiceInterface;
use App\Services\BaseService;

class OurGoalService extends BaseService implements OurGoalServiceInterface
{
    protected OurGoalRepositoryInterface $OurGoalRepository;

    public function __construct(OurGoalRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->OurGoalRepository = $repository;
    }
}
