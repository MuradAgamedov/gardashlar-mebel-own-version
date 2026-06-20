<?php

namespace App\Interfaces\Repositories\Front;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface RoluteeteActionRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveActions(int $languageId);
    public function addUserAction(int $userId, int $actionId);
    public function getUserActions(int $userId, int $languageId);
}
