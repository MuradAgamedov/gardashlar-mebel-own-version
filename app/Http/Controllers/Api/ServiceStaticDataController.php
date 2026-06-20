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

    public function index(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        $cacheKey = "service_static_data_{$languageId}";

        $serviceStaticData = Cache::remember($cacheKey, 3600, function () use ($languageId) {
            return $this->serviceStaticDataService->first(['translates' => fn($q) => $q->where('language_id', $languageId)]);
        });

        if (!$serviceStaticData) {
            return response()->json(['success' => false, 'message' => 'Service static data not found'], 404);
        }

        if ($serviceStaticData->translates->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Translation not found for the specified language'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service static data retrieved successfully',
            'data' => new ServiceStaticDataResource($serviceStaticData),
        ]);
    }
}
