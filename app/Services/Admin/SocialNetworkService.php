<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\SocialNetworkRepositoryInterface;
use App\Interfaces\Services\Admin\SocialNetworkServiceInterface;
use App\Services\BaseService;

class SocialNetworkService extends BaseService implements SocialNetworkServiceInterface
{
    protected SocialNetworkRepositoryInterface $socialNetworkRepository;

    public function __construct(SocialNetworkRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->socialNetworkRepository = $repository;
    }
}
