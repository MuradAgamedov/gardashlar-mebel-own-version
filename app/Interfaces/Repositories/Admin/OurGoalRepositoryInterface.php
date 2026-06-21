<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface OurGoalRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getOurGoals();

    public function createOurGoal(array $data, array $translations);

    public function updateOurGoal(\App\Models\OurGoal $ourGoal, array $data, array $translations);

    public function deleteOurGoal(\App\Models\OurGoal $ourGoal);

    public function reorderOurGoals(array $items);
}
