<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\PrivacyPolicyRepositoryInterface;
use App\Models\PrivacyPolicy;
use App\Repositories\BaseRepository;

class PrivacyPolicyRepository extends BaseRepository implements PrivacyPolicyRepositoryInterface
{
    public function __construct(PrivacyPolicy $model)
    {
        parent::__construct($model);
    }
}
