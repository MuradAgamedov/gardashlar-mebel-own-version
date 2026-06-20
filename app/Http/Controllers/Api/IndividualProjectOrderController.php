<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\IndividualProjectOrderResource;
use App\Interfaces\Services\Front\IndividualProjectOrderServiceInterface;
use App\Models\IndividualProjectOrder;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class IndividualProjectOrderController extends Controller
{
    use LocaleFromHeader;

    protected IndividualProjectOrderServiceInterface $individualProjectOrderService;

    public function __construct(IndividualProjectOrderServiceInterface $individualProjectOrderService)
    {
        $this->individualProjectOrderService = $individualProjectOrderService;
    }


    public function index(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        
        $cacheKey = "individual_project_orders_{$languageId}";
        
        $orders = Cache::remember($cacheKey, 3600, function () use ($languageId) {
            return IndividualProjectOrder::active()
                ->ordered()
                ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                ->get();
        });

        if ($orders->isEmpty()) {
            return $this->notFound('No individual project orders found');
        }

        return $this->success(IndividualProjectOrderResource::collection($orders), 'Individual project orders retrieved successfully');
    }
} 