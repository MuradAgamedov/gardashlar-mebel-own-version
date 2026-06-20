<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\KivCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\KivCategoryServiceInterface;
use App\Services\BaseService;

class KivCategoryService extends BaseService implements KivCategoryServiceInterface
{
    protected KivCategoryRepositoryInterface $KivCategoryRepository;

    public function __construct(KivCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->KivCategoryRepository = $repository;
    }
}
