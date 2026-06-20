<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\IndividualProjectOrderRepositoryInterface;
use App\Interfaces\Services\Admin\IndividualProjectOrderServiceInterface;
use App\Services\BaseService;

class IndividualProjectOrderService extends BaseService implements IndividualProjectOrderServiceInterface
{
    protected IndividualProjectOrderRepositoryInterface $IndividualProjectOrderRepository;

    public function __construct(IndividualProjectOrderRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->IndividualProjectOrderRepository = $repository;
    }
}
