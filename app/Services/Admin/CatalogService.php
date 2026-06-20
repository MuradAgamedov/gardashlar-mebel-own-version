<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\CatalogRepositoryInterface;
use App\Interfaces\Services\Admin\CatalogServiceInterface;
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
