<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Repositories\OrderRepository;

class PaymentController extends Controller
{
    public function __construct(public OrderRepository $repository)
    {
    }

    public function index(Request $request)
    {
        // pagination
        $orders = $this->repository->getOrders();

        return view("admin.orders.index", compact('orders'));
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);

        // AJAX qaytarsın (modal üçün)
        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
}
