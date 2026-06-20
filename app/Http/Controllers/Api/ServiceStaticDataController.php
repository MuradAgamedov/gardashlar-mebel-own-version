<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\ServiceStaticDataServiceInterface;
use App\Http\Resources\Api\ServiceStaticDataResource;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServiceStaticDataController extends Controller
{
    use LocaleFromHeader;

    protected ServiceStaticDataServiceInterface $serviceStaticDataService;

    public function __construct(ServiceStaticDataServiceInterface $serviceStaticDataService)
    {
        $this->serviceStaticDataService = $serviceStaticDataService;
    }

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        $cacheKey = "service_static_data_{$languageId}";

        $serviceStaticData = Cache::remember($cacheKey, 3600, function () use ($languageId) {
            return $this->serviceStaticDataService->first(['translates' => fn($q) => $q->where('language_id', $languageId)]);
        });

        if (!$serviceStaticData) {
            return $this->notFound('Service static data not found');
        }

        if ($serviceStaticData->translates->isEmpty()) {
            return $this->notFound('Translation not found for the specified language');
        }

        return $this->success(new ServiceStaticDataResource($serviceStaticData), 'Service static data retrieved successfully');
    }
}
