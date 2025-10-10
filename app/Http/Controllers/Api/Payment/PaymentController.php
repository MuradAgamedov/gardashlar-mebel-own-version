<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Product;

use App\Models\Order;
use App\Models\CreditCard;
use App\Traits\LocaleFromHeader;
use App\Helpers\PaymentLoginHelper;

class PaymentController extends Controller
{
    use LocaleFromHeader;

    private array $paymentMethods = [
        1 => 'cash',
        2 => 'online',
        3 => 'installment',
    ];

    private array $deliveryMethods = [
        1 => 'store',
        2 => 'delivery',
    ];

    /**
     * 🔹 Nağd ödəniş
     */
    public function startCash(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
        ]);

        $order = Order::where('order_id', $data['order_id'])->first();

        $order->update([
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'status' => 'processing',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nağd ödəniş təsdiqləndi',
            'data' => [
                'order_id' => $order->id,
                'order_number' => $order->order_id,
                'status' => 'processing',
                'payment_status' => 'pending',
            ]
        ]);
    }

    /**
     * 🔹 Online ödəniş
     */
    public function startOnline(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
        ]);

        $order = Order::where('order_id', $data['order_id'])->first();

        $baseUrl = env('UNITED_PAYMENT_API_URL', 'https://test-vpos.unitedpayment.az/api');
        $payload = [
            'amount'     => (float) $order->amount,
            'memberId'   => $order->selected_card,
            'language'   => $order->language ?? 'EN',
            'successUrl' => route('payment.success', ['order_id' => $order->id], true),
            'cancelUrl'  => route('payment.cancel', ['order_id' => $order->id], true),
            'declineUrl' => route('payment.decline', ['order_id' => $order->id], true),
        ];

        $order->update([
            'payment_method' => 'online',
            'payment_status' => 'pending',
            'status' => 'processing',
        ]);

        $response = PaymentLoginHelper::withAuth()->post($baseUrl.'/transactions/pay', $payload);

        if ($response->successful()) {
            $body = $response->json();
            $redirectUrl = $body['url'] ?? null;

            if ($redirectUrl) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $redirectUrl,
                    'order_id' => $order->id,
                ]);
            }

            Log::error('Online Payment: URL not found', ['body' => $response->body()]);
            return response()->json(['success' => false, 'message' => 'Ödəniş linki tapılmadı'], 422);
        }

        Log::error('Online Payment failed', ['body' => $response->body()]);
        return response()->json(['success' => false, 'message' => 'Ödəniş sorğusu uğursuz oldu'], 500);
    }

    /**
     * 🔹 Kredit (taksit) ödənişi
     */
    public function startInstallment(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
        ]);

        $order = Order::where('order_id', $data['order_id'])->first();

        $baseUrl = env('UNITED_PAYMENT_API_URL', 'https://test-vpos.unitedpayment.az/api');

        $payload = [
            'amount'      => (float) $order->amount,
            'memberId'    => $order->selected_card,
            'language'    => $order->language ?? 'EN',
            'installment' => (int) $order->month,
            'successUrl'  => route('payment.success', ['order_id' => $order->id], true),
            'cancelUrl'   => route('payment.cancel', ['order_id' => $order->id], true),
            'declineUrl'  => route('payment.decline', ['order_id' => $order->id], true),
        ];

        $order->update([
            'payment_method' => 'installment',
            'payment_status' => 'pending',
            'status'         => 'processing',
        ]);

        $response = PaymentLoginHelper::withAuth()->post($baseUrl.'/transactions/taksit', $payload);

        if ($response->successful()) {
            $body = $response->json();
            $redirectUrl = $body['url'] ?? null;

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
     * 🔹 payment_method-ə görə yönləndirmə
     */
    public function pay(Request $request)
    {
        try {
            $data = $request->validate([
                'order_id' => 'required|exists:orders,order_id',
            ]);
    
            $order = Order::where('order_id', $data['order_id'])->first();
    
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sifariş tapılmadı',
                ], 404);
            }
    
            $method = is_numeric($order->payment_method)
                ? $this->paymentMethods[(int)$order->payment_method] ?? null
                : $order->payment_method;
    
            if (!$method) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ödəniş metodu düzgün deyil',
                ], 400);
            }
    
            // Cavabı JSON olaraq qaytar
            return match ($method) {
                'cash'        => $this->startCash($request),
                'online'      => $this->startOnline($request),
                'installment' => $this->startInstallment($request),
                default       => response()->json([
                    'success' => false,
                    'message' => 'Uyğun ödəniş metodu tapılmadı',
                ], 400),
            };
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    

    /**
     * 🔹 Müvəqqəti sifariş yaradılması
     */
    public function createTemporaryOrder(Request $request)
    {
        try {
            $data = $request->validate([
                'order_id'        => "nullable|string",
                'product_ids'     => 'required|array|min:1',
                'product_ids.*'   => 'exists:products,id',
                'full_name'       => 'required|string|max:255',
                'phone'           => 'required|string|max:20',
                'email'           => 'required|email|max:255',
                'city'            => 'required|string|max:255',
                'address'         => 'nullable|string',
                'amount'          => 'nullable|numeric',
                'months'          => 'nullable|in:2,3,6,9,12',
                'language'        => 'nullable|in:az,en,ru',
                'delivery_method' => 'required|integer|in:' . implode(',', array_keys($this->deliveryMethods)),
                'payment_method'  => 'required|integer|in:' . implode(',', array_keys($this->paymentMethods)),
                'selected_card'   => 'nullable|integer|exists:credit_cards,id',
                "text" => 'nullable|string',
            ]);
    
            $deliveryMethodName = $this->deliveryMethods[$data['delivery_method']];
            $paymentMethodName  = $this->paymentMethods[$data['payment_method']];
    
            // Kredit kartın adı (əgər varsa)
            $cardId = null;
            if (!empty($data['selected_card'])) {
                $card = CreditCard::find($data['selected_card']);
                $cardId = $card?->id;
            }
    
            // Aylıq məbləğ hesablanır, əgər months gəlməyibsə null olacaq
            $monthValue = $data['months'] ?? null;
            $monthlyPrice = $monthValue ? ($data['amount'] / $monthValue) : null;
    
            $userId = optional($request->user())->id;
    
            if (!empty($data['order_id'])) {
                $order = Order::where('order_id', $data['order_id'])->firstOrFail();
                $order->update([
                    'product_ids'     => json_encode($data['product_ids']),
                    'full_name'       => $data['full_name'],
                    'phone'           => $data['phone'],
                    'email'           => $data['email'],
                    'city'            => $data['city'],
                    'text'            => $data['text'],

                    'address'         => $data['address'] ?? null,
                    'delivery_method' => $deliveryMethodName,
                    'payment_method'  => $paymentMethodName,
                    'selected_card'   => $cardId,
                    'month'           => $monthValue,           // 👈 burda null da yaza bilər
                    'monthly_price'   => $monthlyPrice,
                    'amount'          => $data['amount'],
                    'language'        => $data['language'] ?? 'EN',
                    'status'          => 'temporary',
                    'payment_status'  => 'unpaid',
                ]);
                $message = 'Mövcud sifariş yeniləndi';
            } else {
                $order = Order::create([
                    'user_id'         => $userId,
                    'product_ids'     => json_encode($data['product_ids']),
                    'full_name'       => $data['full_name'],
                    'phone'           => $data['phone'],
                    'email'           => $data['email'],
                    'city'            => $data['city'],
                    'address'         => $data['address'] ?? null,
                    'order_id'        => strtoupper(Str::random(12)),
                    'delivery_method' => $deliveryMethodName,
                    'payment_method'  => $paymentMethodName,
                    'selected_card'   => $cardId,
                    'month'           => $monthValue,           // 👈 burada da null ola bilər
                    'monthly_price'   => $monthlyPrice,
                    'amount'          => $data['amount'],
                    'language'        => $data['language'] ?? 'EN',
                    'status'          => 'temporary',
                    'payment_status'  => 'unpaid',
                    'text'            => $data['text'],
                ]);
                $message = 'Yeni müvəqqəti sifariş yaradıldı';
            }
    
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'order_id' => $order->order_id,
              
                ]
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Temporary order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    


    /**
     * 🔹 Get order details by order_id
     */
    public function getOrder(Request $request, $orderId)
    {
        try {
            $order = Order::where('order_id', $orderId)
                ->select([
                    'order_id',
                    'full_name',
                    'phone',
                    'email',
                    'city',
                    'address',
                    'delivery_method',
                    'payment_method',
                    'selected_card',
                    'month',
                    'amount',
                    'text'     
                ])
                ->firstOrFail();

           

            return response()->json([
                'success' => true,
                'data' => $order
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sifariş tapılmadı'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Order retrieval failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Xəta baş verdi'
            ], 500);
        }
    }

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
}
