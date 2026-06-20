<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\HomePageProjectCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\HomePageProjectCategoryServiceInterface;
use App\Services\BaseService;

class HomePageProjectCategoryService extends BaseService implements HomePageProjectCategoryServiceInterface
{
    protected HomePageProjectCategoryRepositoryInterface $HomePageProjectCategoryRepository;

    public function __construct(HomePageProjectCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->HomePageProjectCategoryRepository = $repository;
    }
}
