<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BecomeAPartnerRepositoryInterface;
use App\Interfaces\Services\Admin\BecomeAPartnerServiceInterface;
use App\Services\BaseService;

class BecomeAPartnerService extends BaseService implements BecomeAPartnerServiceInterface
{
    protected BecomeAPartnerRepositoryInterface $BecomeAPartnerRepository;

    public function __construct(BecomeAPartnerRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->BecomeAPartnerRepository = $repository;
    }
}
