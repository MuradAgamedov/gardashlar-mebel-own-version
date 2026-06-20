<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\BecomeAPartnerAdvantageRepositoryInterface;
use App\Models\BecomeAPartnerAdvantage;
use App\Repositories\BaseRepository;

class BecomeAPartnerAdvantageRepository extends BaseRepository implements BecomeAPartnerAdvantageRepositoryInterface
{
    public function __construct(BecomeAPartnerAdvantage $model)
    {
        parent::__construct($model);
    }
}
