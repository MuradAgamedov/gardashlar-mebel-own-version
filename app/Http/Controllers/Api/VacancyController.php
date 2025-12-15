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

        $data = Cache::rememberForever($cacheKey, function () use ($slug, $languageId) {

            $vacancy = Vacancy::with([
                    'translates.language',
                    'requirements.translates.language'
                ])
                ->whereHas('translates', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                })
                ->active()
                ->notExpired()
                ->first();

            if (!$vacancy) {
                return null;
            }

            $translation = $vacancy->translates
                ->firstWhere('language_id', $languageId);

            // 🔹 Slug translations
            $slugTranslations = $vacancy->translates->mapWithKeys(function ($t) {
                return [$t->language->lang_code => $t->slug];
            });

            // 🔹 Requirements (main vacancy)
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

            // 🔹 Other vacancies (FULL STRUCTURE)
            $otherVacancies = Vacancy::with([
                    'translates.language',
                    'requirements.translates.language'
                ])
                ->where('id', '!=', $vacancy->id)
                ->active()
                ->notExpired()
                ->ordered()
                ->limit(5)
                ->get()
                ->map(function ($item) use ($languageId) {

                    $t = $item->translates
                        ->firstWhere('language_id', $languageId);

                    $slugTranslations = $item->translates->mapWithKeys(function ($tr) {
                        return [$tr->language->lang_code => $tr->slug];
                    });

                    $isExpired = $item->last_apply_date
                        ? now()->greaterThan($item->last_apply_date)
                        : false;

                    return [
                        'id' => $item->id,
                        'title' => $t?->title ?? '',
                        'slug' => $t?->slug ?? '',
                        'position' => $t?->position ?? '',
                        'experience' => $t?->experience ?? '',
                        'work_schedule' => $t?->work_schedule ?? '',
                        'salary' => $t?->salary ?? '',
                        'email' => $item->email,
                        'phone' => $item->phone,
                        'address' => $t?->address ?? '',
                        'last_apply_date' => $item->last_apply_date?->format('Y-m-d'),
                        'is_expired' => $isExpired,
                        'requirements' => [], // 👉 listdə adətən boş saxlanır
                        'created_at' => $item->created_at?->toISOString(),
                        'seo_title' => $t?->seo_title ?? '',
                        'seo_description' => $t?->seo_description ?? '',
                        'seo_keywords' => $t?->seo_keywords ?? '',
                        'translates' => [
                            'slug' => $slugTranslations
                        ]
                    ];
                });

            // 🔹 MAIN vacancy (index ilə eyni)
            return [
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
                'is_expired' => false,
                'requirements' => $requirements,
                'created_at' => $vacancy->created_at?->toISOString(),
                'seo_title' => $translation?->seo_title ?? '',
                'seo_description' => $translation?->seo_description ?? '',
                'seo_keywords' => $translation?->seo_keywords ?? '',
                'translates' => [
                    'slug' => $slugTranslations
                ],

                // ✅ FULL IVacancy list
                'other_vacancies' => $otherVacancies
            ];
        });

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Vacancy not found'
            ], 404);
        }

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