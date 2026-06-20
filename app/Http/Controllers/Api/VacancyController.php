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
                return $this->notFound('No vacancies found');
            }

            return $this->success(VacancyResource::collection($vacancies), 'Vacancies retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving vacancies: ' . $e->getMessage());
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
                return $this->notFound('Vacancy not found');
            }

            $resource = new VacancyDetailResource($vacancy);
            $resource->languageId = $languageId;

            return $this->success($resource, 'Vacancy retrieved successfully');

        } catch (\Throwable $e) {
            return $this->serverError('Error retrieving vacancy: ' . $e->getMessage());
        }
    }
}
