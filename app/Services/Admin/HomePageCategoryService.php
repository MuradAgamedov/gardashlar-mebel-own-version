<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\HomePageCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\HomePageCategoryServiceInterface;
use App\Services\BaseService;

class HomePageCategoryService extends BaseService implements HomePageCategoryServiceInterface
{
    protected HomePageCategoryRepositoryInterface $HomePageCategoryRepository;

    public function __construct(HomePageCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->HomePageCategoryRepository = $repository;
    }
}
