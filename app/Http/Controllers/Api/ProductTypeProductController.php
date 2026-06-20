<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductCardResource;
use App\Interfaces\Services\Front\ProductTypeServiceInterface;
use App\Interfaces\Services\Front\ProductServiceInterface;
use App\Models\ProductCompare;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class ProductTypeProductController extends Controller
{
    use LocaleFromHeader;

    protected ProductTypeServiceInterface $productTypeService;
    protected ProductServiceInterface $productService;

    public function __construct(
        ProductTypeServiceInterface $productTypeService,
        ProductServiceInterface $productService
    ) {
        $this->productTypeService = $productTypeService;
        $this->productService = $productService;
    }

    public function index(Request $request, $productTypeId): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $productType = $this->productTypeService->first([], [
                'id' => $productTypeId,
                'status' => true
            ]);

            if (!$productType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product type not found'
                ], 404);
            }

            $cacheKey = "api:product-type:{$productTypeId}:products:lang:{$languageId}";

            $products = Cache::rememberForever($cacheKey, function () use ($productTypeId, $languageId) {
                return $this->productService->getProductsByProductType((int)$productTypeId, $languageId);
            });

            // 🔐 Auth user
            $user = auth('sanctum')->user();

            $cartIds = $favIds = $compareIds = [];

            if ($user && $products->isNotEmpty()) {
                $productIds = $products->pluck('id');

                $cartIds = \App\Models\Cart::where('user_id', $user->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();

                $favIds = \App\Models\Favorite::where('user_id', $user->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();

                $compareIds = ProductCompare::where('user_id', $user->id)
                    ->whereIn('product_id', $productIds)
                    ->pluck('product_id')
                    ->toArray();
            }

            $request->attributes->set('cartIds', $cartIds);
            $request->attributes->set('favIds', $favIds);
            $request->attributes->set('compareIds', $compareIds);

            return response()->json([
                'success' => true,
                'data'    => ProductCardResource::collection($products),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
