<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ServiceListResource;
use App\Http\Resources\Api\ServiceDetailResource;
use App\Interfaces\Services\Front\ServiceServiceInterface;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ServiceController extends Controller
{
    use LocaleFromHeader;

    protected ServiceServiceInterface $serviceService;

    public function __construct(ServiceServiceInterface $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            if (!$languageId) {
                return $this->error('Invalid language', 400);
            }

            $cacheKey = "services_api_{$languageId}";

            $services = Cache::remember($cacheKey, 3600, function () use ($languageId) {
                return $this->serviceService->getActiveOrderedWithTranslations($languageId);
            });

            return $this->success(ServiceListResource::collection($services), 'Services retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving services: ' . $e->getMessage());
        }
    }

    public function show(Request $request, $slug): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            if (!$languageId) {
                return $this->error('Invalid language', 400);
            }

            $cacheKey = "service_detail_{$slug}_{$languageId}";

            $service = Cache::remember($cacheKey, 3600, function () use ($slug, $languageId) {
                return $this->serviceService->getServiceBySlugWithRelations($slug, $languageId);
            });

            if (!$service) {
                return $this->notFound('Service not found');
            }

            $resource = new ServiceDetailResource($service);
            $resource->languageId = $languageId;

            return $this->success($resource, 'Service retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving service: ' . $e->getMessage());
        }
    }
}
