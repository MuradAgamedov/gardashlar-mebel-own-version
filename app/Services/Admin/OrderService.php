<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\OrderRepositoryInterface;
use App\Interfaces\Services\Admin\OrderServiceInterface;
use App\Services\BaseService;

class OrderService extends BaseService implements OrderServiceInterface
{
    protected OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->OrderRepository = $repository;
    }

    public function getOrders($request = null)
    {
        return $this->OrderRepository->getOrders($request);
    }

    public function findOrder($id)
    {
        return $this->OrderRepository->findOrder($id);
    }
}
