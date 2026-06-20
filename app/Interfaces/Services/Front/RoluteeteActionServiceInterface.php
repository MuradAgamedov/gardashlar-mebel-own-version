<?php

namespace App\Interfaces\Services\Front;

use App\Interfaces\Services\BaseServiceInterface;

interface RoluteeteActionServiceInterface extends BaseServiceInterface
{
    public function getActiveActions(int $languageId);
    public function addUserAction(int $userId, int $actionId);
    public function getUserActions(int $userId, int $languageId);
}
