<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Services\Admin\OrderServiceInterface;

class PaymentController extends Controller
{
    public function __construct(protected OrderServiceInterface $orderService)
    {
    }

    public function index(Request $request)
    {
        $orders = $this->paymentService->getOrders($request);
        return view("admin.orders.index", compact('orders'));
    }

    public function show($id)
    {
        $order = $this->paymentService->findOrder($id);
        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
}
