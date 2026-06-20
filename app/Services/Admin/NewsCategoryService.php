<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\NewsCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\NewsCategoryServiceInterface;
use App\Services\BaseService;

class NewsCategoryService extends BaseService implements NewsCategoryServiceInterface
{
    protected NewsCategoryRepositoryInterface $NewsCategoryRepository;

    public function __construct(NewsCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->NewsCategoryRepository = $repository;
    }
}
