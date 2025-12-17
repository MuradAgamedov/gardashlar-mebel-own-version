<?php
namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Global\BaseRepository;

class OrderRepository extends BaseRepository
{
    public function __construct(public Order $order)
    {
    }

    public function getOrders($request = null)
    {
        $query = $this->order->latest();
        
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
        return $this->order->findOrFail($id);
    }
}
