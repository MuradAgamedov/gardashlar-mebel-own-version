<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\BecomeAInvestorRepositoryInterface;
use App\Interfaces\Services\Front\BecomeAInvestorServiceInterface;
use App\Services\BaseService;

class BecomeAInvestorService extends BaseService implements BecomeAInvestorServiceInterface
{
    protected BecomeAInvestorRepositoryInterface $becomeAInvestorRepository;

    public function __construct(BecomeAInvestorRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->becomeAInvestorRepository = $repository;
    }
}
