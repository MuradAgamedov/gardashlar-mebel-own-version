<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\BecomeAPartnerRepositoryInterface;
use App\Models\BecomeAPartner;
use App\Repositories\BaseRepository;

class BecomeAPartnerRepository extends BaseRepository implements BecomeAPartnerRepositoryInterface
{
    public function __construct(BecomeAPartner $model)
    {
        parent::__construct($model);
    }
}
