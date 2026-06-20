<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SocialNetwork\GetByTypesRequest;
use App\Http\Resources\Api\SocialNetworkResource;
use App\Models\SocialNetwork;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;


class SocialNetworkController extends Controller
{
   
    public function index(): JsonResponse
    {
        $cacheKey = 'social_networks_active';
        
        $socialNetworks = Cache::remember($cacheKey, 3600, function () {
            return SocialNetwork::active()->ordered()->get();
        });

        return response()->json([
            'success' => true,
            'data' => SocialNetworkResource::collection($socialNetworks)
        ]);
    }

    public function show(string $type): JsonResponse
    {
        $cacheKey = "social_network_{$type}";
        
        $socialNetwork = Cache::remember($cacheKey, 3600, function () use ($type) {
            return SocialNetwork::getByType($type);
        });

        if (!$socialNetwork) {
            return response()->json([
                'success' => false,
                'message' => 'Social network not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new SocialNetworkResource($socialNetwork)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/social-networks/by-types",
     *     summary="Get social networks by array of types",
     *     tags={"Social Networks"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="types", type="array", @OA\Items(type="string"), example={"facebook", "instagram", "twitter"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Social networks by types",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object", example={
     *                 "facebook": {
     *                     "id": 1,
     *                     "type": "facebook",
     *                     "name": "Facebook",
     *                     "icon": "fab fa-facebook",
     *                     "url": "https://facebook.com/company",
     *                     "order": 1,
     *                     "color": "#1877F2"
     *                 },
     *                 "instagram": {
     *                     "id": 2,
     *                     "type": "instagram",
     *                     "name": "Instagram",
     *                     "icon": "fab fa-instagram",
     *                     "url": "https://instagram.com/company",
     *                     "order": 2,
     *                     "color": "#E4405F"
     *                 }
     *             })
     *         )
     *     )
     * )
     */
    public function getByTypesArray(GetByTypesRequest $request): JsonResponse
    {
        $types = $request->types;
        $cacheKey = 'social_networks_by_types_' . md5(serialize($types));
        
        $socialNetworks = Cache::remember($cacheKey, 3600, function () use ($types) {
            return SocialNetwork::getByTypes($types)->keyBy('type');
        });

        $data = $socialNetworks->map(fn($sn) => new SocialNetworkResource($sn));

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

  
    public function getAvailableTypes(): JsonResponse
    {
        $cacheKey = 'social_networks_available_types';
        
        $types = Cache::remember($cacheKey, 3600, function () {
            return SocialNetwork::getAvailableTypes();
        });

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }
}
