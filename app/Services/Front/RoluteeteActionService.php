<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\RoluteeteActionRepositoryInterface;
use App\Interfaces\Services\Front\RoluteeteActionServiceInterface;
use App\Services\BaseService;

class RoluteeteActionService extends BaseService implements RoluteeteActionServiceInterface
{
    protected RoluteeteActionRepositoryInterface $roluteeteActionRepository;

    public function __construct(RoluteeteActionRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->roluteeteActionRepository = $repository;
    }
}
