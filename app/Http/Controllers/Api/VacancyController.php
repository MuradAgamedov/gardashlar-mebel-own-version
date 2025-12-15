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

        $cacheKey = "api_vacancy_show_{$slug}_{$languageId}";

        $vacancy = Cache::rememberForever($cacheKey, function () use ($slug, $languageId) {

            return Vacancy::with([
                    'translates.language',
                    'requirements.translates.language'
                ])
                ->whereHas('translates', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                })
                ->active()
                ->first();
        });

        if (!$vacancy) {
            return response()->json([
                'success' => false,
                'message' => 'Vacancy not found'
            ], 404);
        }

        $translation = $vacancy->translates
            ->firstWhere('language_id', $languageId);

        // 🔹 Expired hesablanması (index ilə eyni)
        $isExpired = $vacancy->last_apply_date
            ? now()->greaterThan($vacancy->last_apply_date)
            : false;

        // 🔹 Slug translations (index-dəki kimi)
        $slugTranslations = $vacancy->translates->mapWithKeys(function ($t) {
            return [$t->language->lang_code => $t->slug];
        });

        // 🔹 Requirements
        $requirements = $vacancy->requirements()
            ->with(['translates' => fn ($q) => $q->where('language_id', $languageId)])
            ->active()
            ->ordered()
            ->get()
            ->map(function ($req) {
                $t = $req->translates->first();
                return [
                    'id' => $req->id,
                    'title' => $t?->title ?? '',
                    'description' => $t?->description ?? '',
                ];
            });

        // 🔹 INDEX STRUCTURE ilə eyni obyekt
        $data = [
            'id' => $vacancy->id,
            'title' => $translation?->title ?? '',
            'slug' => $translation?->slug ?? '',
            'position' => $translation?->position ?? '',
            'experience' => $translation?->experience ?? '',
            'work_schedule' => $translation?->work_schedule ?? '',
            'salary' => $translation?->salary ?? '',
            'email' => $vacancy->email,
            'phone' => $vacancy->phone,
            'address' => $translation?->address ?? '',
            'last_apply_date' => $vacancy->last_apply_date?->format('Y-m-d'),
            'is_expired' => $isExpired,

            'requirements' => $requirements,

            'created_at' => $vacancy->created_at?->toISOString(),

            'seo_title' => $translation?->seo_title ?? '',
            'seo_description' => $translation?->seo_description ?? '',
            'seo_keywords' => $translation?->seo_keywords ?? '',

            'translates' => [
                'slug' => $slugTranslations
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Vacancy retrieved successfully',
            'data' => $data
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving vacancy',
            'error' => $e->getMessage()
        ], 500);
    }
}



} 