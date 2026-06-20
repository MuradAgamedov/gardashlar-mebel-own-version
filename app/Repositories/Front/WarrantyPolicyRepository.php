<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\WarrantyPolicyRepositoryInterface;
use App\Models\WarrantyPolicy;
use App\Repositories\BaseRepository;

class WarrantyPolicyRepository extends BaseRepository implements WarrantyPolicyRepositoryInterface
{
    public function __construct(WarrantyPolicy $model)
    {
        parent::__construct($model);
    }
}
