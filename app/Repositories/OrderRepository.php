<?php
namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Global\BaseRepository;

class OrderRepository extends BaseRepository
{
    public function __construct(public Order $order)
    {
    }

    public function getOrders()
    {
        return $this->order->latest()->paginate(10);
    }

    public function findOrder($id)
    {
        return $this->order->findOrFail($id);
    }
}
