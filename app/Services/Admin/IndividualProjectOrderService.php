<?php

namespace App\Services\Admin;

use App\Interfaces\Repositories\Admin\IndividualProjectOrderRepositoryInterface;
use App\Interfaces\Services\Admin\IndividualProjectOrderServiceInterface;
use App\Services\BaseService;

class IndividualProjectOrderService extends BaseService implements IndividualProjectOrderServiceInterface
{
    protected IndividualProjectOrderRepositoryInterface $IndividualProjectOrderRepository;

    public function __construct(IndividualProjectOrderRepositoryInterface $repository)
    {
        parent::__construct($repository);
        $this->IndividualProjectOrderRepository = $repository;
    }

    public function getIndividualProjectOrders($request)
    {
        return $this->IndividualProjectOrderRepository->getIndividualProjectOrders($request);
    }

    public function createIndividualProjectOrder(array $data, array $translates)
    {
        return $this->IndividualProjectOrderRepository->createIndividualProjectOrder($data, $translates);
    }

    public function updateIndividualProjectOrder(\App\Models\IndividualProjectOrder $individualProjectOrder, array $data, array $translates)
    {
        return $this->IndividualProjectOrderRepository->updateIndividualProjectOrder($individualProjectOrder, $data, $translates);
    }

    public function deleteIndividualProjectOrder(\App\Models\IndividualProjectOrder $individualProjectOrder)
    {
        return $this->IndividualProjectOrderRepository->deleteIndividualProjectOrder($individualProjectOrder);
    }

    public function reorderIndividualProjectOrders(array $order)
    {
        return $this->IndividualProjectOrderRepository->reorderIndividualProjectOrders($order);
    }

    public function deleteSelected(array $ids)
    {
        return $this->IndividualProjectOrderRepository->deleteSelected($ids);
    }
}
