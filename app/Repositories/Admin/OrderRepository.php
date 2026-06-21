<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\OrderRepositoryInterface;
use App\Models\Order;
use App\Repositories\BaseRepository;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function getOrders($request = null)
    {
        $query = $this->model->newQuery()->latest();

        if ($request) {
            if ($request->filled('delivery_method')) {
                $query->where('delivery_method', $request->delivery_method);
            }

            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }
        }

        return $query->paginate(10);
    }

    public function findOrder($id)
    {
        return $this->model->newQuery()->findOrFail($id);
    }
}
