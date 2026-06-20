<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductTagRepositoryInterface;
use App\Interfaces\Services\Admin\ProductTagServiceInterface;
use App\Services\BaseService;

class ProductTagService extends BaseService implements ProductTagServiceInterface
{
    protected ProductTagRepositoryInterface $ProductTagRepository;

    public function __construct(ProductTagRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProductTagRepository = $repository;
    }
}
