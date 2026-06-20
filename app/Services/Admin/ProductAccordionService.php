<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\ProductAccordionRepositoryInterface;
use App\Interfaces\Services\Admin\ProductAccordionServiceInterface;
use App\Services\BaseService;

class ProductAccordionService extends BaseService implements ProductAccordionServiceInterface
{
    protected ProductAccordionRepositoryInterface $ProductAccordionRepository;

    public function __construct(ProductAccordionRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->ProductAccordionRepository = $repository;
    }
}
