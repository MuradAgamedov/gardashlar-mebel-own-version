<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\OurGoalRepositoryInterface;
use App\Interfaces\Services\Front\OurGoalServiceInterface;
use App\Services\BaseService;

class OurGoalService extends BaseService implements OurGoalServiceInterface
{
    protected OurGoalRepositoryInterface $ourGoalRepository;

    public function __construct(OurGoalRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ourGoalRepository = $repository;
    }
}
