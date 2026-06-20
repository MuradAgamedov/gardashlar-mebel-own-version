<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\HomePageCategoryRepositoryInterface;
use App\Interfaces\Services\Front\HomePageCategoryServiceInterface;
use App\Services\BaseService;

class HomePageCategoryService extends BaseService implements HomePageCategoryServiceInterface
{
    protected HomePageCategoryRepositoryInterface $homePageCategoryRepository;

    public function __construct(HomePageCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->homePageCategoryRepository = $repository;
    }
}
