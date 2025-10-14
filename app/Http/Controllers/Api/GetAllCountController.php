<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Favorite;
use App\Models\ProductCompare;

class GetAllCountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // 🔹 Sayları götür
        $cartCount = Cart::where('user_id', $user->id)->count();
        $favoriteCount = Favorite::where('user_id', $user->id)->count();
        $compareCount = ProductCompare::where('user_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'message' => 'Counts fetched successfully.',
            'data' => [
                'cart_count' => $cartCount,
                'favorite_count' => $favoriteCount,
                'compare_count' => $compareCount,
            ],
        ]);
    }
}
