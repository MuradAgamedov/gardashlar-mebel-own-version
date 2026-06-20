<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\OrderRepositoryInterface;
use App\Interfaces\Services\Admin\OrderServiceInterface;
use App\Services\BaseService;

class OrderService extends BaseService implements OrderServiceInterface
{
    protected OrderRepositoryInterface $OrderRepository;

    public function __construct(OrderRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->OrderRepository = $repository;
    }
}
