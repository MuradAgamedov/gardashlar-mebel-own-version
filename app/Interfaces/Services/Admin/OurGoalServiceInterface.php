<?php

namespace App\Interfaces\Services\Admin;

use App\Interfaces\Services\BaseServiceInterface;

interface OurGoalServiceInterface extends BaseServiceInterface
{
    //

    public function getOurGoals();

    public function createOurGoal(array $data, array $translations);

    public function updateOurGoal(\App\Models\OurGoal $ourGoal, array $data, array $translations);

    public function deleteOurGoal(\App\Models\OurGoal $ourGoal);

    public function reorderOurGoals(array $items);
}
