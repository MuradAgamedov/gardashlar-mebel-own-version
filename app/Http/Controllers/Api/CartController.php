<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Traits\LocaleFromHeader;

class CartController extends Controller
{
    use LocaleFromHeader;

    public function addCart(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        $user = $request->user();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "You must be logged in to complete this transaction!"
            ], 401);
        }

        // Cart əlavə et və ya yenilə
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

    public function getCart(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        $user = $request->user();

        $carts = $user->cart()->with(['product.projectCategory', 'product.images'])->get();

        $cartData = $carts->map(function ($cart) use ($languageId) {
            return [
                'cart_id' => $cart->id,
                'count' => $cart->count,
                'product' => $cart->product ? $cart->product->apiData($languageId) : null,
                'color_id' => $cart->color_id,
            ];
        });

        return response()->json([
            'success' => true,
            "data" => $cartData,
            "message" => true
        ]);
    }

    public function removeCart(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                "success" => false,
                "message" => "You must be logged in!"
            ], 401);
        }

        $deleted = Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->delete();

        return response()->json([
            'success' => (bool) $deleted,
            'message' => $deleted ? 'Product removed from cart successfully!' : 'Product not found in cart!'
        ]);
    }




    public function checkCardProducts(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'product_ids' => 'required|array|min:1|max:50',
                'product_ids.*' => 'required|integer|min:1'
            ]);
    
            $productIds = $request->input('product_ids', []);
            $user = $request->user();
            $languageId = $this->getLanguageIdFromHeader($request);
    
            // İstifadəçi yoxlanışı
            if ($user) {
                $compareProductIds = ProductCompare::where('user_id', $user->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();
            } else {
                // Qeydiyyatsız user üçün session istifadə edilmir
                $compareProductIds = $productIds; 
            }
    
            // Məhsulları DB-dən götür
            $products = Product::whereIn('id', $compareProductIds)
                ->active()
                ->with([
                    'translates' => fn($q) => $q->where('language_id', $languageId),
                    'images' => fn($q) => $q->where('status', true)->orderBy('order'),
                    'categories.translates' => fn($q) => $q->where('language_id', $languageId),
                ])
                ->get()
                ->map(function ($product) {
                    $translation = $product->translates->first();
                    $mainImage = $product->images->where('is_main', true)->first();
                    if (!$mainImage) $mainImage = $product->images->first();
    
                    $category = $product->categories->first();
                    $categorySlug = $category ? $category->translates->first()?->slug : null;
                    $parentSlug = $category && $category->parent_id
                        ? $category->parent->translates->first()?->slug
                        : null;
    
                    return [
                        'id' => $product->id,
                        'title' => $translation?->title ?? '',
                        'slug' => $translation?->slug ?? '',
                        'price' => $product->price,
                        'discount_percent' => $product->discount_percent,
                        'category_slug' => $categorySlug,
                        'parent_slug' => $parentSlug,
                        'images' => $product->images->map(fn($img) => [
                            'id' => $img->id,
                            'image_url' => $img->image ? asset('storage/' . $img->image) : null,
                            'is_main' => $img->is_main
                        ]),
                    ];
                });
    
            // Tapılmayan məhsullar
            $existingProductIds = $products->pluck('id')->toArray();
            $nonExistingProducts = array_diff($productIds, $existingProductIds);
    
            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $products,
                    'not_found' => array_values($nonExistingProducts)
                ]
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors()['product_ids'] ?? []),
                'data' => null
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking compare products: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
    

}
