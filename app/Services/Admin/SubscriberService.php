<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\SubscriberRepositoryInterface;
use App\Interfaces\Services\Admin\SubscriberServiceInterface;
use App\Services\BaseService;

class SubscriberService extends BaseService implements SubscriberServiceInterface
{
    protected SubscriberRepositoryInterface $SubscriberRepository;

    public function __construct(SubscriberRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->SubscriberRepository = $repository;
    }
}
