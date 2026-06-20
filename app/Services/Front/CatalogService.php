<?php

namespace App\Services\Front;

use App\Interfaces\Repositories\Front\CatalogRepositoryInterface;
use App\Interfaces\Services\Front\CatalogServiceInterface;
use App\Services\BaseService;

class CatalogService extends BaseService implements CatalogServiceInterface
{
    protected CatalogRepositoryInterface $catalogRepository;

    public function __construct(CatalogRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->catalogRepository = $repository;
    }
}
