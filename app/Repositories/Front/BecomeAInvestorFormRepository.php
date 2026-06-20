<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\BecomeAInvestorFormRepositoryInterface;
use App\Models\BecomeAInvestorForm;
use App\Repositories\BaseRepository;

class BecomeAInvestorFormRepository extends BaseRepository implements BecomeAInvestorFormRepositoryInterface
{
    public function __construct(BecomeAInvestorForm $model)
    {
        parent::__construct($model);
    }
}
