<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\HomePageDiscountRepositoryInterface;
use App\Interfaces\Services\Admin\HomePageDiscountServiceInterface;
use App\Services\BaseService;

class HomePageDiscountService extends BaseService implements HomePageDiscountServiceInterface
{
    protected HomePageDiscountRepositoryInterface $HomePageDiscountRepository;

    public function __construct(HomePageDiscountRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->HomePageDiscountRepository = $repository;
    }
}
