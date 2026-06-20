<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\HomePageProjectCategoryRepositoryInterface;
use App\Interfaces\Services\Front\HomePageProjectCategoryServiceInterface;
use App\Services\BaseService;

class HomePageProjectCategoryService extends BaseService implements HomePageProjectCategoryServiceInterface
{
    protected HomePageProjectCategoryRepositoryInterface $HomePageProjectCategoryRepository;

    public function __construct(HomePageProjectCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $HomePageProjectCategoryRepository = $repository;
    }
}
