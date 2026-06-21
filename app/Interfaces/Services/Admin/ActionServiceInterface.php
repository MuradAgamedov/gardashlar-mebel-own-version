<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface ActionServiceInterface extends BaseServiceInterface
{
    //

    public function getAction($request);

    public function createAction(array $data, array $translates);

    public function updateAction(\App\Models\Action $action, array $data, array $translates);

    public function deleteAction(\App\Models\Action $action);
}
