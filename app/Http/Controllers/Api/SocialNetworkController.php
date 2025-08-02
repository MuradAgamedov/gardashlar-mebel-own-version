<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SocialNetwork;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Social Networks",
 *     description="API Endpoints for Social Networks"
 * )
 */
class SocialNetworkController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/social-networks",
     *     summary="Get all active social networks",
     *     tags={"Social Networks"},
     *     @OA\Response(
     *         response=200,
     *         description="List of social networks",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="type", type="string", example="facebook"),
     *                 @OA\Property(property="name", type="string", example="Facebook"),
     *                 @OA\Property(property="icon", type="string", example="fab fa-facebook"),
     *                 @OA\Property(property="url", type="string", example="https://facebook.com/company"),
     *                 @OA\Property(property="order", type="integer", example=1),
     *                 @OA\Property(property="color", type="string", example="#1877F2")
     *             ))
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $cacheKey = 'social_networks_active';
        
        $socialNetworks = Cache::remember($cacheKey, 3600, function () {
            return SocialNetwork::active()
                ->ordered()
                ->get()
                ->map(function ($socialNetwork) {
                    return [
                        'id' => $socialNetwork->id,
                        'type' => $socialNetwork->type,
                        'name' => $socialNetwork->display_name,
                        'icon' => $socialNetwork->display_icon,
                        'url' => $socialNetwork->url,
                        'order' => $socialNetwork->order,
                        'color' => $socialNetwork->color
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'data' => $socialNetworks
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/social-networks/{type}",
     *     summary="Get social network by type",
     *     tags={"Social Networks"},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Social network type (e.g., facebook, instagram)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Social network details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="type", type="string", example="facebook"),
     *                 @OA\Property(property="name", type="string", example="Facebook"),
     *                 @OA\Property(property="icon", type="string", example="fab fa-facebook"),
     *                 @OA\Property(property="url", type="string", example="https://facebook.com/company"),
     *                 @OA\Property(property="order", type="integer", example=1),
     *                 @OA\Property(property="color", type="string", example="#1877F2")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Social network not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Social network not found")
     *         )
     *     )
     * )
     */
    public function show(string $type): JsonResponse
    {
        $cacheKey = "social_network_{$type}";
        
        $socialNetwork = Cache::remember($cacheKey, 3600, function () use ($type) {
            return SocialNetwork::active()
                ->where('type', $type)
                ->first();
        });

        if (!$socialNetwork) {
            return response()->json([
                'success' => false,
                'message' => 'Social network not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $socialNetwork->id,
                'type' => $socialNetwork->type,
                'name' => $socialNetwork->display_name,
                'icon' => $socialNetwork->display_icon,
                'url' => $socialNetwork->url,
                'order' => $socialNetwork->order,
                'color' => $socialNetwork->color
            ]
        ]);
    }
}
