<?php

namespace App\Repositories\Front;

use App\Interfaces\Repositories\Front\SubscriberRepositoryInterface;
use App\Models\Subscriber;
use App\Repositories\BaseRepository;

class SubscriberRepository extends BaseRepository implements SubscriberRepositoryInterface
{
    public function __construct(Subscriber $model)
    {
        parent::__construct($model);
    }
}
