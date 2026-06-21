<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\OurGoalRepositoryInterface;
use App\Interfaces\Services\Admin\OurGoalServiceInterface;
use App\Services\BaseService;

class OurGoalService extends BaseService implements OurGoalServiceInterface
{
    protected OurGoalRepositoryInterface $OurGoalRepository;

    public function __construct(OurGoalRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->OurGoalRepository = $repository;
    }

    public function getOurGoals()
    {
        return $this->OurGoalRepository->getOurGoals();
    }

    public function createOurGoal(array $data, array $translations)
    {
        return $this->OurGoalRepository->createOurGoal($data, $translations);
    }

    public function updateOurGoal(\App\Models\OurGoal $ourGoal, array $data, array $translations)
    {
        return $this->OurGoalRepository->updateOurGoal($ourGoal, $data, $translations);
    }

    public function deleteOurGoal(\App\Models\OurGoal $ourGoal)
    {
        return $this->OurGoalRepository->deleteOurGoal($ourGoal);
    }

    public function reorderOurGoals(array $items)
    {
        return $this->OurGoalRepository->reorderOurGoals($items);
    }
}
