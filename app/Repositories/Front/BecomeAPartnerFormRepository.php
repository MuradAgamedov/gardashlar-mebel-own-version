<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\BecomeAPartnerFormRepositoryInterface;
use App\Models\BecomeAPartnerForm;
use App\Repositories\BaseRepository;

class BecomeAPartnerFormRepository extends BaseRepository implements BecomeAPartnerFormRepositoryInterface
{
    public function __construct(BecomeAPartnerForm $model)
    {
        parent::__construct($model);
    }
}
