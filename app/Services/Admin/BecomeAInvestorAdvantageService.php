<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BecomeAInvestorAdvantageRepositoryInterface;
use App\Interfaces\Services\Admin\BecomeAInvestorAdvantageServiceInterface;
use App\Services\BaseService;

class BecomeAInvestorAdvantageService extends BaseService implements BecomeAInvestorAdvantageServiceInterface
{
    protected BecomeAInvestorAdvantageRepositoryInterface $BecomeAInvestorAdvantageRepository;

    public function __construct(BecomeAInvestorAdvantageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->BecomeAInvestorAdvantageRepository = $repository;
    }
}
