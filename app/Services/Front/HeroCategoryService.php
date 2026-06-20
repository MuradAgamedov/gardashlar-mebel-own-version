<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\HeroCategoryRepositoryInterface;
use App\Interfaces\Services\Front\HeroCategoryServiceInterface;
use App\Services\BaseService;

class HeroCategoryService extends BaseService implements HeroCategoryServiceInterface
{
    protected HeroCategoryRepositoryInterface $heroCategoryRepository;

    public function __construct(HeroCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->heroCategoryRepository = $repository;
    }
}
