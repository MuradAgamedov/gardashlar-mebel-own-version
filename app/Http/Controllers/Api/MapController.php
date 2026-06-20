<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\MapServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Maps",
 *     description="API Endpoints for Maps"
 * )
 */
class MapController extends Controller
{

    protected MapServiceInterface $mapService;

    public function __construct(MapServiceInterface $mapService)
    {
        $this->mapService = $mapService;
    }

    /**
     * @OA\Get(
     *     path="/api/maps",
     *     summary="Get all active maps",
     *     description="Returns list of active maps ordered by order field",
     *     tags={"Maps"},
     *     @OA\Response(
     *         response=200,
     *         description="Maps retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Maps retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="map", type="string", example="<iframe src='https://www.google.com/maps/embed?pb=...' width='100%' height='450' style='border:0;' allowfullscreen='' loading='lazy' referrerpolicy='no-referrer-when-downgrade'></iframe>")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No maps found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No maps found")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $cacheKey = 'api_maps';
            
            $maps = Cache::rememberForever($cacheKey, function () {
                return Map::select(['id', 'map'])
                    ->where('status', true)
                    ->orderBy('order')
                    ->get();
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
                'data' => $maps
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