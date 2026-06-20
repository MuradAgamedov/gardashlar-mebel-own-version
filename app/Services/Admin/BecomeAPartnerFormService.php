<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BecomeAPartnerFormRepositoryInterface;
use App\Interfaces\Services\Admin\BecomeAPartnerFormServiceInterface;
use App\Services\BaseService;

class BecomeAPartnerFormService extends BaseService implements BecomeAPartnerFormServiceInterface
{
    protected BecomeAPartnerFormRepositoryInterface $BecomeAPartnerFormRepository;

    public function __construct(BecomeAPartnerFormRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->BecomeAPartnerFormRepository = $repository;
    }
}
