<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ActionRepositoryInterface;
use App\Interfaces\Services\Admin\ActionServiceInterface;
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
