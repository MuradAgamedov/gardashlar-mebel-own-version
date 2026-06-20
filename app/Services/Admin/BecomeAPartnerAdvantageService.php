<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\BecomeAPartnerAdvantageRepositoryInterface;
use App\Interfaces\Services\Admin\BecomeAPartnerAdvantageServiceInterface;
use App\Services\BaseService;

class BecomeAPartnerAdvantageService extends BaseService implements BecomeAPartnerAdvantageServiceInterface
{
    protected BecomeAPartnerAdvantageRepositoryInterface $BecomeAPartnerAdvantageRepository;

    public function __construct(BecomeAPartnerAdvantageRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->BecomeAPartnerAdvantageRepository = $repository;
    }
}
