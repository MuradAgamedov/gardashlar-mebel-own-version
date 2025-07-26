<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class LanguageController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/languages",
     *     operationId="getLanguages",
     *     tags={"Languages"},
     *     summary="Get all active languages",
     *     description="Returns a list of active languages ordered by 'order' field",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Azərbaycan dili"),
     *                     @OA\Property(property="lang_code", type="string", example="az"),
     *                     @OA\Property(property="lang_name", type="string", example="Azerbaijani"),
     *                     @OA\Property(property="is_default", type="boolean", example=true),
     *                     @OA\Property(property="order", type="integer", example=1)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving languages")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Set locale from header
            $this->setLocaleFromHeader($request);
            
            $languages = Cache::rememberForever('api_languages', function () {
                return Language::where('status', true)
                    ->orderBy('order')
                    ->select(['id', 'title', 'lang_code', 'lang_name', 'is_default', 'order'])
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $languages
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving languages: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
