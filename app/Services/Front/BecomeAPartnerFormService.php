<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\BecomeAPartnerFormRepositoryInterface;
use App\Interfaces\Services\Front\BecomeAPartnerFormServiceInterface;
use App\Services\BaseService;

class BecomeAPartnerFormService extends BaseService implements BecomeAPartnerFormServiceInterface
{
    protected BecomeAPartnerFormRepositoryInterface $becomeAPartnerFormRepository;

    public function __construct(BecomeAPartnerFormRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->becomeAPartnerFormRepository = $repository;
    }
}
