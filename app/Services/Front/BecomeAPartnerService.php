<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\BecomeAPartnerRepositoryInterface;
use App\Interfaces\Services\Front\BecomeAPartnerServiceInterface;
use App\Services\BaseService;

class BecomeAPartnerService extends BaseService implements BecomeAPartnerServiceInterface
{
    protected BecomeAPartnerRepositoryInterface $becomeAPartnerRepository;

    public function __construct(BecomeAPartnerRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->becomeAPartnerRepository = $repository;
    }
}
