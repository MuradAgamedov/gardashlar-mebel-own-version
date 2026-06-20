<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\HeroProductRepositoryInterface;
use App\Interfaces\Services\Admin\HeroProductServiceInterface;
use App\Services\BaseService;

class HeroProductService extends BaseService implements HeroProductServiceInterface
{
    protected HeroProductRepositoryInterface $HeroProductRepository;

    public function __construct(HeroProductRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->HeroProductRepository = $repository;
    }
}
