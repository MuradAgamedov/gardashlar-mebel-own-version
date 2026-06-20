<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\HomePageDiscountServiceInterface;
use App\Http\Resources\Api\HomePageDiscountResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class HomePageDiscountController extends Controller
{
    use LocaleFromHeader;

    protected HomePageDiscountServiceInterface $homePageDiscountService;

    public function __construct(HomePageDiscountServiceInterface $homePageDiscountService)
    {
        $this->homePageDiscountService = $homePageDiscountService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:home-page-discount:lang:{$languageId}";

            $homePageDiscount = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->homePageDiscountService->first(['translates' => fn($q) => $q->where('language_id', $languageId)]);
            });

            if (!$homePageDiscount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Home Page Discount data not found',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new HomePageDiscountResource($homePageDiscount),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving Home Page Discount data: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
