<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\BecomeAInvestorFormRepositoryInterface;
use App\Interfaces\Services\Front\BecomeAInvestorFormServiceInterface;
use App\Services\BaseService;

class BecomeAInvestorFormService extends BaseService implements BecomeAInvestorFormServiceInterface
{
    protected BecomeAInvestorFormRepositoryInterface $becomeAInvestorFormRepository;

    public function __construct(BecomeAInvestorFormRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->becomeAInvestorFormRepository = $repository;
    }
}
