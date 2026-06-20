<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\BecomeAPartnerAdvantageRepositoryInterface;
use App\Interfaces\Services\Front\BecomeAPartnerAdvantageServiceInterface;
use App\Services\BaseService;

class BecomeAPartnerAdvantageService extends BaseService implements BecomeAPartnerAdvantageServiceInterface
{
    protected BecomeAPartnerAdvantageRepositoryInterface $becomeAPartnerAdvantageRepository;

    public function __construct(BecomeAPartnerAdvantageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->becomeAPartnerAdvantageRepository = $repository;
    }
}
