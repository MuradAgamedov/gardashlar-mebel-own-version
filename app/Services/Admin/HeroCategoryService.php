<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\HeroCategoryRepositoryInterface;
use App\Interfaces\Services\Admin\HeroCategoryServiceInterface;
use App\Services\BaseService;

class HeroCategoryService extends BaseService implements HeroCategoryServiceInterface
{
    protected HeroCategoryRepositoryInterface $HeroCategoryRepository;

    public function __construct(HeroCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->HeroCategoryRepository = $repository;
    }
}
