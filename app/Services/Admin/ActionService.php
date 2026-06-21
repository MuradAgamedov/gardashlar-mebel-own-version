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

    public function getAction($request)
    {
        return $this->actionRepository->getAction($request);
    }

    public function createAction(array $data, array $translates)
    {
        return $this->actionRepository->createAction($data, $translates);
    }

    public function updateAction(\App\Models\Action $action, array $data, array $translates)
    {
        return $this->actionRepository->updateAction($action, $data, $translates);
    }

    public function deleteAction(\App\Models\Action $action)
    {
        return $this->actionRepository->deleteAction($action);
    }
}
