<?php

namespace App\Interfaces\Repositories\Admin;

use App\Interfaces\Repositories\BaseRepositoryInterface;

interface IndividualProjectOrderRepositoryInterface extends BaseRepositoryInterface
{
    //

    public function getIndividualProjectOrders($request);

    public function createIndividualProjectOrder(array $data, array $translates);

    public function updateIndividualProjectOrder(\App\Models\IndividualProjectOrder $individualProjectOrder, array $data, array $translates);

    public function deleteIndividualProjectOrder(\App\Models\IndividualProjectOrder $individualProjectOrder);

    public function reorderIndividualProjectOrders(array $order);

    public function deleteSelected(array $ids);
}
