<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\SocialNetworkRepositoryInterface;
use App\Models\SocialNetwork;
use App\Repositories\BaseRepository;

class SocialNetworkRepository extends BaseRepository implements SocialNetworkRepositoryInterface
{
    public function __construct(SocialNetwork $model)
    {
        parent::__construct($model);
    }
}
