<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\RefundPolicyRepositoryInterface;
use App\Models\RefundPolicy;
use App\Repositories\BaseRepository;

class RefundPolicyRepository extends BaseRepository implements RefundPolicyRepositoryInterface
{
    public function __construct(RefundPolicy $model)
    {
        parent::__construct($model);
    }
}
