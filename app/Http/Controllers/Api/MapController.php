<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MapResource;
use App\Interfaces\Services\Front\MapServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;


class MapController extends Controller
{

    protected MapServiceInterface $mapService;

    public function __construct(MapServiceInterface $mapService)
    {
        $this->mapService = $mapService;
    }

   
    public function index(Request $request): JsonResponse
    {
        try {
            $cacheKey = 'api_maps';
            
            $maps = Cache::rememberForever($cacheKey, function () {
                return $this->mapService->getActiveOrdered();
            });

            if ($maps->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No maps found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Maps retrieved successfully',
                'data' => MapResource::collection($maps)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving maps: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }


} 