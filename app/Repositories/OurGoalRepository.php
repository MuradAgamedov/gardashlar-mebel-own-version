<?php
namespace App\Repositories;

use App\Models\OurGoal;
use App\Repositories\Global\BaseRepository;

class OurGoalRepository extends BaseRepository
{

    public function __construct(public OurGoal $ourGoal)
    {
    }

    // Bütün məqsədləri gətir
    public function getOurGoals()
    {
        return $this->GetData($this->ourGoal, ['translates.language'])->get();
    }
}
