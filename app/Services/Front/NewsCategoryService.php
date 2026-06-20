<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\NewsCategoryRepositoryInterface;
use App\Interfaces\Services\Front\NewsCategoryServiceInterface;
use App\Services\BaseService;

class NewsCategoryService extends BaseService implements NewsCategoryServiceInterface
{
    protected NewsCategoryRepositoryInterface $newsCategoryRepository;

    public function __construct(NewsCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->newsCategoryRepository = $repository;
    }
}
