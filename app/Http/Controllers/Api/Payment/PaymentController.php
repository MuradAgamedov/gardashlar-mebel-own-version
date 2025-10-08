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
     * Installment əməliyyatını başlat
     */
    public function startInstallment(Request $request)
    {
        $data = $request->validate([
            'product_id'      => 'required|exists:products,id',
            'full_name'       => 'required|string|max:255',
            'phone'           => 'required|string|max:50',
            'email'           => 'required|email|max:255',
            'city'            => 'required|string|max:255',
            'address'         => 'nullable|string',
            'amount'          => 'required|numeric|min:1',
            'months'          => 'required|in:2,3,6,9,12',
            'member_id'       => 'required|string',
            'language'        => 'nullable|in:AZ,EN,RU',
        ]);

        $lang = $data['language'] ?? 'EN';
        $baseUrl = env('UNITED_PAYMENT_API_URL', 'https://test-vpos.unitedpayment.az/api');

        // Create order
        $order = Order::create([
            'user_id'        => $request->user()->id ?? null,
            'product_id'     => $data['product_id'],
            'full_name'      => $data['full_name'],
            'phone'          => $data['phone'],
            'email'          => $data['email'],
            'city'           => $data['city'],
            'address'        => $data['address'] ?? null,
            'order_id'       => strtoupper(Str::random(12)),
            'delivery_method'=> 'installment',
            'payment_method' => 'card',
            'selected_card'  => $data['member_id'],
            'month'          => $data['months'],
            'monthly_price'  => $data['amount'] / $data['months'],
            'status'         => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $payload = [
            'amount'      => (float) $data['amount'],
            'memberId'    => $data['member_id'],
            'language'    => $lang,
            'installment' => (int) $data['months'],
            'successUrl'  => route('payment.success', ['order_id' => $order->id], true),
            'cancelUrl'   => route('payment.cancel', ['order_id' => $order->id], true),
            'declineUrl'  => route('payment.decline', ['order_id' => $order->id], true),
        ];

        $response = PaymentLoginHelper::withAuth()->post($baseUrl.'/transactions/taksit', $payload);

        if ($response->successful()) {
            $body = $response->json();
            $redirectUrl = is_array($body) ? ($body['url'] ?? null) : (string) $response->body();

            if ($redirectUrl) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $redirectUrl,
                    'order_id' => $order->id,
                ]);
            }

            Log::error('Installment: URL not found', ['body' => $response->body()]);
            return response()->json(['success' => false, 'message' => 'Ödəniş linki tapılmadı'], 422);
        }

        Log::error('Installment failed', ['body' => $response->body()]);
        return response()->json(['success' => false, 'message' => 'Ödəniş sorğusu uğursuz oldu'], 500);
    }

    /**
     * Nağd ödəniş (birdəfəlik)
     */
    public function startCash(Request $request)
    {
        $data = $request->validate([
            'product_id'      => 'required|exists:products,id',
            'full_name'       => 'required|string|max:255',
            'phone'           => 'required|string|max:50',
            'email'           => 'required|email|max:255',
            'city'            => 'required|string|max:255',
            'address'         => 'nullable|string',
            'amount'          => 'required|numeric|min:1',
            'member_id'       => 'required|string',
            'language'        => 'nullable|in:AZ,EN,RU',
        ]);

        $lang = $data['language'] ?? 'EN';
        $baseUrl = env('UNITED_PAYMENT_API_URL', 'https://test-vpos.unitedpayment.az/api');

        // Create order
        $order = Order::create([
            'user_id'        => $request->user()->id ?? null,
            'product_id'     => $data['product_id'],
            'full_name'      => $data['full_name'],
            'phone'          => $data['phone'],
            'email'          => $data['email'],
            'city'           => $data['city'],
            'address'        => $data['address'] ?? null,
            'order_id'       => strtoupper(Str::random(12)),
            'delivery_method'=> 'cash',
            'payment_method' => 'cash',
            'selected_card'  => $data['member_id'],
            'status'         => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $payload = [
            'amount'     => (float) $data['amount'],
            'memberId'   => $data['member_id'],
            'language'   => $lang,
            'successUrl' => route('payment.success', ['order_id' => $order->id], true),
            'cancelUrl'  => route('payment.cancel', ['order_id' => $order->id], true),
            'declineUrl' => route('payment.decline', ['order_id' => $order->id], true),
        ];

        $response = PaymentLoginHelper::withAuth()->post($baseUrl.'/transactions/pay', $payload);

        if ($response->successful()) {
            $body = $response->json();
            $redirectUrl = is_array($body) ? ($body['url'] ?? null) : (string) $response->body();

            if ($redirectUrl) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $redirectUrl,
                    'order_id' => $order->id,
                ]);
            }

            Log::error('Cash Payment: URL not found', ['body' => $response->body()]);
            return response()->json(['success' => false, 'message' => 'Ödəniş linki tapılmadı'], 422);
        }

        Log::error('Cash Payment failed', ['body' => $response->body()]);
        return response()->json(['success' => false, 'message' => 'Ödəniş sorğusu uğursuz oldu'], 500);
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
                'product_id'      => 'required|exists:products,id',
                'full_name'       => 'nullable|string|max:255',
                'phone'           => 'nullable|string|max:50',
                'address'         => 'nullable|string',
                'total_price'     => 'required|numeric|min:0',
            ]);

            // Create temporary order
            $order = Order::create([
                'user_id'        => $request->user()->id ?? null,
                'product_id'     => $data['product_id'],
                'full_name'      => $data['full_name'] ?? null,
                'phone'          => $data['phone'] ?? null,
                'email'          => null,
                'city'           => null,
                'address'        => $data['address'] ?? null,
                'order_id'       => strtoupper(Str::random(12)),
                'delivery_method'=> null,
                'payment_method' => null,
                'selected_card'  => null,
                'month'          => null,
                'monthly_price'  => null,
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
