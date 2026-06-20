<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\IndividualProjectOrderRepositoryInterface;
use App\Interfaces\Services\Front\IndividualProjectOrderServiceInterface;
use App\Services\BaseService;

class IndividualProjectOrderService extends BaseService implements IndividualProjectOrderServiceInterface
{
    protected IndividualProjectOrderRepositoryInterface $individualProjectOrderRepository;

    public function __construct(IndividualProjectOrderRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->individualProjectOrderRepository = $repository;
    }
}
