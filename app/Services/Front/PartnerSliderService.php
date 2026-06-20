<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\PartnerSliderRepositoryInterface;
use App\Interfaces\Services\Front\PartnerSliderServiceInterface;
use App\Services\BaseService;

class PartnerSliderService extends BaseService implements PartnerSliderServiceInterface
{
    protected PartnerSliderRepositoryInterface $partnerSliderRepository;

    public function __construct(PartnerSliderRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->partnerSliderRepository = $repository;
    }
}
