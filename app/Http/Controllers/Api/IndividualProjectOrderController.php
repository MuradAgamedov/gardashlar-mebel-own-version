<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IndividualProjectOrder;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Individual Project Orders",
 *     description="API Endpoints for Individual Project Orders"
 * )
 */
class IndividualProjectOrderController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/individual-project-orders",
     *     summary="Get all active individual project orders",
     *     description="Returns list of active individual project orders with translated content based on header language",
     *     tags={"Individual Project Orders"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Individual project orders retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Individual project orders retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Layihə Təklifi Göndərin"),
     *                     @OA\Property(property="description", type="string", example="Öz layihənizi təsvir edin və biz sizinlə əlaqə saxlayacağıq")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No individual project orders found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No individual project orders found")
     *         )
     *     )
     * )
     */
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