<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BecomeAInvestorRepositoryInterface;
use App\Interfaces\Services\Admin\BecomeAInvestorServiceInterface;
use App\Services\BaseService;

class BecomeAInvestorService extends BaseService implements BecomeAInvestorServiceInterface
{
    protected BecomeAInvestorRepositoryInterface $becomeAInvestorRepository;

    public function __construct(BecomeAInvestorRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->becomeAInvestorRepository = $repository;
    }

    public function updateBecomeAInvestor($becomeAInvestor, array $translations)
    {
        return $this->becomeAInvestorRepository->updateBecomeAInvestor($becomeAInvestor, $translations);
    }
}
