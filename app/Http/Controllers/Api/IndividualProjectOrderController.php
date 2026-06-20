<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\IndividualProjectOrderServiceInterface;
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
        
        return Cache::remember($cacheKey, 3600, function () use ($languageId) {
            $orders = IndividualProjectOrder::active()
                ->ordered()
                ->with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->get();
            
            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No individual project orders found'
                ], 404);
            }
            
            $data = $orders->map(function ($order) {
                $translation = $order->translates->first();
                
                return [
                    'id' => $order->id,
                    'title' => $translation ? $translation->title : null,
                    'description' => $translation ? $translation->description : null,
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Individual project orders retrieved successfully',
                'data' => $data
            ]);
        });
    }
} 