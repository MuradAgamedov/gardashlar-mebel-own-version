<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BecomeAInvestorFormRepositoryInterface;
use App\Interfaces\Services\Admin\BecomeAInvestorFormServiceInterface;
use App\Services\BaseService;

class BecomeAInvestorFormService extends BaseService implements BecomeAInvestorFormServiceInterface
{
    protected BecomeAInvestorFormRepositoryInterface $BecomeAInvestorFormRepository;

    public function __construct(BecomeAInvestorFormRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->BecomeAInvestorFormRepository = $repository;
    }
}
