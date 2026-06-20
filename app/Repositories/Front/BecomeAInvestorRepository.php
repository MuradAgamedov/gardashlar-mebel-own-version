<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\BecomeAInvestorRepositoryInterface;
use App\Models\BecomeAInvestor;
use App\Repositories\BaseRepository;

class BecomeAInvestorRepository extends BaseRepository implements BecomeAInvestorRepositoryInterface
{
    public function __construct(BecomeAInvestor $model)
    {
        parent::__construct($model);
    }
}
