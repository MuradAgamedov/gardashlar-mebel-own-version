<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\SubscriberRepositoryInterface;
use App\Interfaces\Services\Front\SubscriberServiceInterface;
use App\Services\BaseService;

class SubscriberService extends BaseService implements SubscriberServiceInterface
{
    protected SubscriberRepositoryInterface $subscriberRepository;

    public function __construct(SubscriberRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->subscriberRepository = $repository;
    }
}
