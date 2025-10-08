<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\PaymentLoginHelper;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Order;
use App\Traits\LocaleFromHeader;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    use LocaleFromHeader;

    /**
     * Mövcud sifariş üçün nağd ödənişi təsdiqlə
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function startCash(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($data['order_id']);

        $order->update([
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'processing',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nağd ödəniş uğurla təsdiqləndi',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_id,
                'payment_status' => 'pending',
                'status' => 'processing'
            ]
        ]);
    }

    /**
     * Mövcud sifariş üçün kredit ödənişini təsdiqlə
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function startInstallment(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($data['order_id']);

        $order->update([
            'payment_method' => 'installment',
            'payment_status' => 'pending',
            'status' => 'processing',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kredit ödənişi uğurla təsdiqləndi',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_id,
                'payment_status' => 'pending',
                'status' => 'processing',
                'cancelUrl' => route('payment.cancel', ['order_id' => $order->id], true),
                'declineUrl' => route('payment.decline', ['order_id' => $order->id], true),
            ]
        ]);
    }

    /**
     * Success nəticəsi
     */
    public function success(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = Order::find($orderId);

        if ($order) {
            $order->update([
                'status' => 'completed',
                'payment_status' => 'paid'
            ]);
        }

        $data = $this->decodeUp($request->query('up'));
        Log::info('Payment Success', ['order_id' => $orderId, 'data' => $data]);

        return view('payment.success', compact('order', 'data'));
    }

    /**
     * Cancel nəticəsi
     */
    public function cancel(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = Order::find($orderId);

        if ($order) {
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'unpaid'
            ]);
        }

        $data = $this->decodeUp($request->query('up'));
        Log::info('Payment Cancel', ['order_id' => $orderId, 'data' => $data]);

        return view('payment.cancel', compact('order', 'data'));
    }

    /**
     * Decline nəticəsi
     */
    public function decline(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = Order::find($orderId);

        if ($order) {
            $order->update([
                'status' => 'failed',
                'payment_status' => 'failed'
            ]);
        }

        $data = $this->decodeUp($request->query('up'));
        Log::info('Payment Decline', ['order_id' => $orderId, 'data' => $data]);

        return view('payment.decline', compact('order', 'data'));
    }

    /**
     * Create temporary order (without payment)
     */
    public function createTemporaryOrder(Request $request)
    {
        try {
            $data = $request->validate([
                'product_ids'     => 'required|array|min:1',
                'product_ids.*'   => 'exists:products,id',
                'full_name'       => 'required|string|max:255',
                'email'           => 'required|email|max:255',
                'city'            => 'required|string|max:255',
                'address'         => 'nullable|string',
                'amount'          => 'required|numeric|min:1',
                'months'          => 'required|in:2,3,6,9,12',
                'member_id'       => 'nullable|string',
                'language'        => 'nullable|in:AZ,EN,RU',
            ]);

            // Calculate monthly price
            $monthlyPrice = $data['amount'] / $data['months'];

            // Get user ID and generate memberId if token exists
            $userId = null;
            $memberId = null;

            if ($request->bearerToken() && $request->user()) {
                $userId = $request->user()->id;
                $memberId = $userId; // Use user ID as member_id if authenticated
            } else {
                // Generate a random 10-digit number for non-authenticated users
                $memberId = mt_rand(1000000000, 9999999999);
            }

            // Create temporary order
            $order = Order::create([
                'user_id'        => $userId,
                'product_ids'    => json_encode($data['product_ids']),
                'full_name'      => $data['full_name'],
                'phone'          => $data['phone'],
                'email'          => $data['email'],
                'city'           => $data['city'],
                'address'        => $data['address'] ?? null,
                'order_id'       => strtoupper(Str::random(12)),
                'delivery_method'=> 'installment',
                'payment_method' => 'card',
                'selected_card'  => $memberId,
                'month'          => $data['months'],
                'monthly_price'  => $monthlyPrice,
                'status'         => 'temporary',
                'payment_status' => 'unpaid',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Müvəqqəti sifariş yaradıldı',
                'data' => [
                    'order_id' => $order->id
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Temporary order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Müvəqqəti sifariş yaradılarkən xəta baş verdi'
            ], 500);
        }
    }

    /**
     * Get temporary order by ID
     */
    public function getTemporaryOrder(Request $request, $orderId)
    {
        try {
            $order = Order::with('product')->find($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sifariş tapılmadı'
                ], 404);
            }

            // Get language ID from header
            $languageId = $this->getLanguageIdFromHeader($request);

            // Get product details
            $product = $order->product;

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Məhsul tapılmadı'
                ], 404);
            }

            // Load product relationships
            $product->load([
                'translates' => fn($q) => $q->where('language_id', $languageId),
                'images' => fn($q) => $q->where('status', true)->orderBy('order'),
                'images.color' => fn($q) => $q->select('product_colors.id', 'product_colors.color_code'),
                'categories.translates' => fn($q) => $q->where('language_id', $languageId),
                'types.translates' => fn($q) => $q->where('language_id', $languageId),
                'colors' => fn($q) => $q->select('product_colors.id', 'product_colors.color_code'),
                'details.translates' => fn($q) => $q->where('language_id', $languageId),
            ]);

            $translation = $product->translates->first();

            // Category & parent category
            $categorySlug = null;
            $categoryTitle = null;
            $parentSlug = null;
            $parentTitle = null;

            if ($product->categories->isNotEmpty()) {
                $category = $product->categories->first();
                $categoryTranslation = $category->translates->first();
                $categorySlug = $categoryTranslation?->slug;
                $categoryTitle = $categoryTranslation?->title;

                if ($category->parent_id) {
                    $parentCategory = ProductCategory::find($category->parent_id);
                    $parentTranslation = $parentCategory?->translates()->where('language_id', $languageId)->first();
                    $parentSlug = $parentTranslation?->slug;
                    $parentTitle = $parentTranslation?->title;
                }
            }

            // Product types
            $productTypes = $product->types->map(fn($type) => [
                'id' => $type->id,
                'title' => $type->translates->first()?->title ?? ''
            ]);

            // Available colors
            $availableColors = $product->colors->map(fn($color) => [
                'id' => $color->id,
                'color_code' => $color->color_code
            ]);

            // Images
            $imagesWithColors = $product->images->map(fn($image) => [
                'id' => $image->id,
                'image_url' => $image->image ? asset('storage/' . $image->image) : null,
                'is_main' => $image->is_main,
                'order' => $image->order,
                'color_id' => $image->product_color_id
            ])->sortBy('order')->values();

            if ($imagesWithColors->isEmpty()) {
                $imagesWithColors = collect([
                    [
                        'id' => time(),
                        'image_url' => asset('images/default_image.webp'),
                        'is_main' => false,
                        'order' => 0,
                        'color_id' => null
                    ]
                ]);
            }

            // Product details
            $productDetails = $product->details->map(fn($detail) => [
                'id' => $detail->id,
                'keyword' => $detail->translate($languageId)?->keyword ?? '',
                'value' => $detail->translate($languageId)?->value ?? '',
            ]);

            // Calculate total price with discount
            $price = $product->price;
            $discountPrice = $product->discount_price;
            $totalPrice = $discountPrice > 0 ? $discountPrice : $price;

            $productData = [
                'id' => $product->id,
                'slug' => $translation?->slug ?? '',
                'title' => $translation?->title ?? '',
                'available_colors' => $availableColors,
                'category_slug' => $categorySlug,
                'category_title' => $categoryTitle,
                'parent_slug' => $parentSlug,
                'parent_title' => $parentTitle,
                'discount_price' => $discountPrice,
                'price' => $price,
                'total_price' => $totalPrice,
                'images' => $imagesWithColors,
                'product_details' => $productDetails,
                'product_types' => $productTypes,
                'view_count' => $product->view_count
            ];

            // Order data
            $orderData = [
                'order_id' => $order->id,
                'full_name' => $order->full_name,
                'phone' => $order->phone,
                'address' => $order->address,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'created_at' => $order->created_at,
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'order' => $orderData,
                    'product' => $productData
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Get temporary order failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Sifariş məlumatları alınarkən xəta baş verdi'
            ], 500);
        }
    }

    /**
     * Get products by IDs
     */
    public function getProducts(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'product_ids' => 'required|array|min:1|max:50',
                'product_ids.*' => 'required|integer|min:1'
            ]);

            $productIds = $request->input('product_ids', []);

            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);

            // Get existing active products with relationships and translations
            $existingProducts = Product::active()
                ->whereIn('id', $productIds)
                ->with([
                    'translates' => fn($q) => $q->where('language_id', $languageId),
                    'images' => fn($q) => $q->where('status', true)->orderBy('order'),
                    'images.color' => fn($q) => $q->select('product_colors.id', 'product_colors.color_code'),
                    'categories.translates' => fn($q) => $q->where('language_id', $languageId),
                    'types.translates' => fn($q) => $q->where('language_id', $languageId),
                    'colors' => fn($q) => $q->select('product_colors.id', 'product_colors.color_code'),

                ])
                ->get();

            // Map to desired response structure
            $productsData = $existingProducts->map(function ($product) use ($languageId) {
                $translation = $product->translates->first();

                // Main image
                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();




                // Available colors
                $availableColors = $product->colors->map(fn($color) => [
                    'id' => $color->id,
                    'color_code' => $color->color_code
                ]);

                // Images
                $imagesWithColors = $product->images->map(fn($image) => [
                    'id' => $image->id,
                    'image_url' => $image->image ? asset('storage/' . $image->image) : null,
                    'is_main' => $image->is_main,
                    'order' => $image->order,
                    'color_id' => $image->product_color_id
                ])->sortBy('order')->values();

                if ($imagesWithColors->isEmpty()) {
                    $imagesWithColors = collect([
                        [
                            'id' => time(),
                            'image_url' => asset('images/default_image.webp'),
                            'is_main' => false,
                            'order' => 0,
                            'color_id' => null
                        ]
                    ]);
                }


                return [
                    'id' => $product->id,
                    'title' => $translation?->title ?? '',
                    'available_colors' => $availableColors,


                    'discount_price' => $product->discount_price,
                    'price' => $product->price,
                    'images' => $imagesWithColors,

                ];
            });

            return response()->json([
                'success' => true,
                'data' => $productsData
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
                'message' => 'Məhsulları yoxlayarkən xəta baş verdi: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    private function decodeUp(?string $base64): array
    {
        if (!$base64) return [];
        try {
            $json = base64_decode($base64, true);
            return json_decode($json, true) ?: [];
        } catch (\Throwable $e) {
            Log::error('UP decode error: '.$e->getMessage());
            return [];
        }
    }
}
