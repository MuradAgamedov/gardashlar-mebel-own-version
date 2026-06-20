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
                return $this->notFound('No maps found');
            }

            return $this->success(MapResource::collection($maps), 'Maps retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving maps: ' . $e->getMessage());
        }
    }


} 