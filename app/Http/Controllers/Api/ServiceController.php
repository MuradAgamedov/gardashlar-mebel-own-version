<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Services",
 *     description="API Endpoints for Services"
 * )
 */
class ServiceController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/services",
     *     summary="Get all active services",
     *     description="Returns list of active services with translated title and card_description",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Services retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Mebel İstehsalı"),
     *                     @OA\Property(property="card_description", type="string", example="Yüksək keyfiyyətli mebel istehsalı")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid language",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid language")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            
            if (!$languageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language'
                ], 400);
            }

            $cacheKey = "services_api_{$languageId}";
            
            $services = Cache::remember($cacheKey, 3600, function () use ($languageId) {
                return Service::active()
                    ->ordered()
                    ->with(['translates' => function ($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    }])
                    ->get()
                    ->map(function ($service) {
                        $translation = $service->translates->first();
                        
                        return [
                            'id' => $service->id,
                            'title' => $translation ? $translation->title : 'No title',
                            'card_description' => $translation ? $translation->card_description : 'No description'
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'message' => 'Services retrieved successfully',
                'data' => $services
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving services: ' . $e->getMessage()
            ], 500);
        }
    }
} 