<?php
namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Global\BaseRepository;

class OrderRepository extends BaseRepository
{

    public function __construct(public Order $order)
    {
    }

    // Sifarişləri paginate ilə gətir
    public function getOrders()
    {
        return $this->order->latest()->paginate(10);
    }
}
