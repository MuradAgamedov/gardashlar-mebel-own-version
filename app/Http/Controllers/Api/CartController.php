<?php

namespace App\Http\Controllers\Api;
use App\Traits\LocaleFromHeader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    use LocaleFromHeader;
    public function addCart(Request $request) {
        $languageId = $this->getLanguageIdFromHeader($request);
        $user = $request->user();
        if(!$user) {
            return response()->json([
                "success" => false,
                "message" => "You must be logged in to complete this transaction!"
            ]);
        }
      

        // Cart əlavə et
        $cart = Cart::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'color_id' => $request->color_id,
            ],
            [
                'count' => $request->count,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'data' => $cart
        ]);
    }


    public function getCart(Request $request) {
        $languageId = $this->getLanguageIdFromHeader($request); // language ID alırıq
        $user = $request->user();
        
        // Cart-ları yüklə, product və productCategory ilə
        $carts = $user->cart()->with(['product.projectCategory', 'product.images'])->get();
    
        // Hər cart item-i JSON formatına çevir
        $cartData = $carts->map(function ($cart) use ($languageId) { // languageId əlavə et
            return [
               "success" => true,
               "data" => [
                'cart_id' => $cart->id,
                'count' => $cart->count,
                'product' => $cart->product ? $cart->product->apiData($languageId) : null,
                'color_id' => $cart->color_id,
               ],
               "message" => "success"
            ];
        });
    
        return response()->json($cartData);
    }
    
    
}
