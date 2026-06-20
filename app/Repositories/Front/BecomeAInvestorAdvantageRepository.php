<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\BecomeAInvestorAdvantageRepositoryInterface;
use App\Models\BecomeAInvestorAdvantage;
use App\Repositories\BaseRepository;

class BecomeAInvestorAdvantageRepository extends BaseRepository implements BecomeAInvestorAdvantageRepositoryInterface
{
    public function __construct(BecomeAInvestorAdvantage $model)
    {
        parent::__construct($model);
    }
}
