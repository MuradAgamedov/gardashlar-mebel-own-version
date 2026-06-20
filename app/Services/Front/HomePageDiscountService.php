<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\HomePageDiscountRepositoryInterface;
use App\Interfaces\Services\Front\HomePageDiscountServiceInterface;
use App\Services\BaseService;

class HomePageDiscountService extends BaseService implements HomePageDiscountServiceInterface
{
    protected HomePageDiscountRepositoryInterface $HomePageDiscountRepository;

    public function __construct(HomePageDiscountRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $HomePageDiscountRepository = $repository;
    }
}
