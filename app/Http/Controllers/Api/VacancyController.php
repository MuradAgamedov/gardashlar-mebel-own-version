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
                ->map(function ($vacancy) use ($languageId) {
                    $translation = $vacancy->translates->first();

                    return [
                        'id' => $vacancy->id,
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

   
   public function show(Request $request, string $slug): JsonResponse
{
    try {
        $languageId = $this->getLanguageIdFromHeader($request);
        
        $cacheKey = "api_vacancy_{$slug}_{$languageId}";
        
        $vacancy = Cache::rememberForever($cacheKey, function () use ($slug, $languageId) {
            return Vacancy::with(['translates.language'])
                ->whereHas('translates', function($query) use ($slug) {
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
            'address' => $vacancy->address,
            'last_apply_date' => $vacancy->last_apply_date?->format('Y-m-d'),
            'created_at' => $vacancy->created_at?->toISOString(),
            'seo_title' => $translation?->seo_title ?? '',
            'seo_description' => $translation?->seo_description ?? '',
            'seo_keywords' => $translation?->seo_keywords ?? '',
            'requirements' => $requirements,
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

} 