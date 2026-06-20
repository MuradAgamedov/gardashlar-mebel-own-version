<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\VacancyServiceInterface;
use App\Http\Resources\Api\VacancyResource;
use App\Http\Resources\Api\VacancyDetailResource;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class VacancyController extends Controller
{
    use LocaleFromHeader;

    protected VacancyServiceInterface $vacancyService;

    public function __construct(VacancyServiceInterface $vacancyService)
    {
        $this->vacancyService = $vacancyService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_vacancies_{$languageId}";

            $vacancies = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->vacancyService->getActiveNotExpiredOrdered($languageId);
            });

            if ($vacancies->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No vacancies found'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Vacancies retrieved successfully',
                'data' => VacancyResource::collection($vacancies),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving vacancies: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_vacancy_show_{$slug}_{$languageId}";

            $vacancy = Cache::rememberForever($cacheKey, function () use ($slug) {
                return $this->vacancyService->getVacancyBySlug($slug);
            });

            if (!$vacancy) {
                return response()->json(['success' => false, 'message' => 'Vacancy not found'], 404);
            }

            $resource = new VacancyDetailResource($vacancy);
            $resource->languageId = $languageId;

            return response()->json([
                'success' => true,
                'message' => 'Vacancy retrieved successfully',
                'data' => $resource,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving vacancy',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
