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
                ->map(function ($vacancy) {
                    $translation = $vacancy->translates->first();

                    $lastApplyDate = $vacancy->last_apply_date 
                        ? $vacancy->last_apply_date->format('Y-m-d')
                        : null;

                    $isExpired = $vacancy->last_apply_date
                        ? now()->greaterThan($vacancy->last_apply_date)
                        : false;

                    return [
                        'id' => $vacancy->id,
                        'title' => $translation?->title ?? '',
                        'slug' => $translation?->slug ?? '',
                        'position' => $translation?->position ?? '',
                        'experience' => $translation?->experience ?? '',
                        'last_apply_date' => $lastApplyDate,
                        'is_expired' => $isExpired,
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


   
public function show(Request $request, string $slug): JsonResponse
{
    try {
        $languageId = $this->getLanguageIdFromHeader($request);

        $cacheKey = "api_vacancy_{$slug}_{$languageId}";

        $vacancy = Cache::rememberForever($cacheKey, function () use ($slug, $languageId) {
            return Vacancy::with(['translates.language', 'requirements.translates.language'])
                ->whereHas('translates', function ($query) use ($slug) {
                    $query->where('slug', $slug);
                })
                ->active()
                ->notExpired()
                ->first();
        });

        if (!$vacancy) {
            return response()->json([
                'success' => false,
                'message' => 'Vacancy not found'
            ], 404);
        }

        $translation = $vacancy->translates->firstWhere('language_id', $languageId);

        // 🔹 Vakansiyanın bütün dillərdəki slug-ları
        $slugTranslations = $vacancy->translates->mapWithKeys(function ($t) {
            return [$t->language->lang_code => $t->slug];
        });

        // 🔹 Tələbləri gətiririk
        $requirements = $vacancy->requirements()
            ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
            ->active()
            ->ordered()
            ->get()
            ->map(function ($requirement) {
                $reqTranslation = $requirement->translates->first();
                return [
                    'id' => $requirement->id,
                    'title' => $reqTranslation?->title ?? '',
                    'description' => $reqTranslation?->description ?? ''
                ];
            });

        // 🔹 Əsas məlumatlar
        $vacancyData = [
            'id' => $vacancy->id,
            'title' => $translation?->title ?? '',
            'slug' => $translation?->slug ?? '',
            'position' => $translation?->position ?? '',
            'experience' => $translation?->experience ?? '',
            'work_schedule' => $translation?->work_schedule ?? '',
            'salary' => $translation?->salary ?? '',
            'email' => $vacancy->email,
            'address' => $translation?->address ?? '', // ✅ düzəldildi
            'last_apply_date' => $vacancy->last_apply_date?->format('Y-m-d'),
            'created_at' => $vacancy->created_at?->toISOString(),
            'seo_title' => $translation?->seo_title ?? '',
            'seo_description' => $translation?->seo_description ?? '',
            'seo_keywords' => $translation?->seo_keywords ?? '',
            'requirements' => $requirements,
            'phone' => $vacancy->phone,

            'translates' => [
                'slug' => $slugTranslations
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Vacancy retrieved successfully',
            'data' => $vacancyData
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving vacancy: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Get other active vacancies (excluding the current one)
 * 
 * @OA\Get(
 *     path="/api/vacancies/other/{slug}",
 *     tags={"Vacancies"},
 *     summary="Get other active vacancies",
 *     @OA\Parameter(
 *         name="slug",
 *         in="path",
 *         required=true,
 *         description="Slug of the current vacancy to exclude"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of other vacancies"
 *     )
 * )
 */
public function otherVacancies(Request $request, string $slug): JsonResponse
{
    try {
        $languageId = $this->getLanguageIdFromHeader($request);
        $cacheKey = "api_other_vacancies_{$slug}_{$languageId}";

        $otherVacancies = Cache::remember($cacheKey, now()->addHours(1), function () use ($slug, $languageId) {
            return Vacancy::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->whereHas('translates', function($query) use ($slug) {
                    $query->where('slug', '!=', $slug);
                })
                ->active()
                ->notExpired()
                ->ordered()
                ->limit(3)
                ->get()
                ->map(function ($vacancy) {
                    $translation = $vacancy->translates->first();

                    return [
                        'id' => $vacancy->id,
                        'title' => $translation?->title ?? '',
                        'slug' => $translation?->slug ?? '',
                        'position' => $translation?->position ?? '',
                        'experience' => $translation?->experience ?? '',
                        'last_apply_date' => $vacancy->last_apply_date?->format('Y-m-d'),
                    ];
                });
        });

        return response()->json([
            'success' => true,
            'message' => 'Other vacancies retrieved successfully',
            'data' => $otherVacancies
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving other vacancies: ' . $e->getMessage()
        ], 500);
    }
}


} 