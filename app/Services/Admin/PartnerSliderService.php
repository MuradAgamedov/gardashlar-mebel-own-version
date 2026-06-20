<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\PartnerSliderRepositoryInterface;
use App\Interfaces\Services\Admin\PartnerSliderServiceInterface;
use App\Services\BaseService;

class PartnerSliderService extends BaseService implements PartnerSliderServiceInterface
{
    protected PartnerSliderRepositoryInterface $PartnerSliderRepository;

    public function __construct(PartnerSliderRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->PartnerSliderRepository = $repository;
    }
}
