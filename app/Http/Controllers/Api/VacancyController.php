<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Vacancies",
 *     description="API Endpoints for Vacancies"
 * )
 */
class VacancyController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/vacancies",
     *     summary="Get all active vacancies",
     *     description="Returns list of active and non-expired vacancies with translated content based on header language",
     *     tags={"Vacancies"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vacancies retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Vacancies retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Mühasib vəzifəsi"),
     *                     @OA\Property(property="slug", type="string", example="muhasib-vezifesi"),
     *                     @OA\Property(property="position", type="string", example="Mühasib"),
     *                     @OA\Property(property="last_apply_date", type="string", format="date", example="2024-12-31"),
     *                     @OA\Property(property="email", type="string", example="hr@company.com"),
     *                     @OA\Property(property="address", type="string", example="Bakı şəhəri"),
     *                     @OA\Property(property="experience", type="string", example="3-5 il"),
     *                     @OA\Property(property="work_schedule", type="string", example="9:00-18:00"),
     *                     @OA\Property(property="is_expired", type="boolean", example=false)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No vacancies found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No vacancies found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving vacancies")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $cacheKey = "api_vacancies_{$languageId}";
            
            $vacancies = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return Vacancy::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->active()
                ->notExpired()
                ->ordered()
                ->get()
                ->map(function ($vacancy) use ($languageId) {
                    $translation = $vacancy->translates->first();

                    return [
                        'title' => $translation ? $translation->title : '',
                        'slug' => $translation ? $translation->slug : '',
                        'position' => $translation ? $translation->position : '',
                        'experience' => $translation ? $translation->experience : '',
                        'last_apply_date' => $vacancy->last_apply_date->format('Y-m-d')
                    ];
                });
            });

            if ($vacancies->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No vacancies found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Vacancies retrieved successfully',
                'data' => $vacancies
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving vacancies: ' . $e->getMessage()
            ], 500);
        }
    }
} 