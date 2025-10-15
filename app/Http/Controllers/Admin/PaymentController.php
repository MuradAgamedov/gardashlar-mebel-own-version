<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\OrderRepository;

class PaymentController extends Controller
{
    public function __construct(public OrderRepository $repository)
    {
    }

    public function index(Request $request)
    {
        $orders = $this->repository->getOrders();
        return view("admin.orders.index", compact('orders'));
    }

    public function show($id)
    {
        $order = $this->repository->findOrder($id);
        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
}
