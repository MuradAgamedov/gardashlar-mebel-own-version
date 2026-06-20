<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\KivCategoryRepositoryInterface;
use App\Interfaces\Services\Front\KivCategoryServiceInterface;
use App\Services\BaseService;

class KivCategoryService extends BaseService implements KivCategoryServiceInterface
{
    protected KivCategoryRepositoryInterface $KivCategoryRepository;

    public function __construct(KivCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $KivCategoryRepository = $repository;
    }
}
