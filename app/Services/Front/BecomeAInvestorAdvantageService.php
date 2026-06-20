<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\BecomeAInvestorAdvantageRepositoryInterface;
use App\Interfaces\Services\Front\BecomeAInvestorAdvantageServiceInterface;
use App\Services\BaseService;

class BecomeAInvestorAdvantageService extends BaseService implements BecomeAInvestorAdvantageServiceInterface
{
    protected BecomeAInvestorAdvantageRepositoryInterface $becomeAInvestorAdvantageRepository;

    public function __construct(BecomeAInvestorAdvantageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->becomeAInvestorAdvantageRepository = $repository;
    }
}
