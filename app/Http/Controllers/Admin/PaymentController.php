<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // pagination
        $orders = Order::latest()->paginate(10);

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
