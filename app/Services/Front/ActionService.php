<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\ActionRepositoryInterface;
use App\Interfaces\Services\Front\ActionServiceInterface;
use App\Services\BaseService;

class ActionService extends BaseService implements ActionServiceInterface
{
    protected ActionRepositoryInterface $actionRepository;

    public function __construct(ActionRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->actionRepository = $repository;
    }
}
