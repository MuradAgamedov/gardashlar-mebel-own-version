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

    public function getActiveActions(int $languageId)
    {
        return $this->roluteeteActionRepository->getActiveActions($languageId);
    }

    public function addUserAction(int $userId, int $actionId)
    {
        return $this->roluteeteActionRepository->addUserAction($userId, $actionId);
    }

    public function getUserActions(int $userId, int $languageId)
    {
        return $this->roluteeteActionRepository->getUserActions($userId, $languageId);
    }
}
