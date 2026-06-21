<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface ActionRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getAction($request);

    public function createAction(array $data, array $translates);

    public function updateAction(\App\Models\Action $action, array $data, array $translates);

    public function deleteAction(\App\Models\Action $action);
}
