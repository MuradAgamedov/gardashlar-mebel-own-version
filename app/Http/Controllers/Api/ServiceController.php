<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ServiceListResource;
use App\Http\Resources\Api\ServiceDetailResource;
use App\Models\Service;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServiceController extends Controller
{
    use LocaleFromHeader;

    public function index(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            if (!$languageId) {
                return response()->json(['success' => false, 'message' => 'Invalid language'], 400);
            }

            $cacheKey = "services_api_{$languageId}";

            $services = Cache::remember($cacheKey, 3600, function () use ($languageId) {
                return Service::active()
                    ->ordered()
                    ->with(['translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->get();
            });

            return response()->json([
                'success' => true,
                'message' => 'Services retrieved successfully',
                'data' => ServiceListResource::collection($services),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving services: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, $slug)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            if (!$languageId) {
                return response()->json(['success' => false, 'message' => 'Invalid language'], 400);
            }

            $cacheKey = "service_detail_{$slug}_{$languageId}";

            $service = Cache::remember($cacheKey, 3600, function () use ($slug, $languageId) {
                return Service::active()
                    ->with([
                        'translates.language',
                        'advantages.translates' => fn($q) => $q->where('language_id', $languageId),
                    ])
                    ->whereHas('translates', fn($q) => $q->where('slug', $slug))
                    ->first();
            });

            if (!$service) {
                return response()->json(['success' => false, 'message' => 'Service not found'], 404);
            }

            $resource = new ServiceDetailResource($service);
            $resource->languageId = $languageId;

            return response()->json([
                'success' => true,
                'message' => 'Service retrieved successfully',
                'data' => $resource,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving service: ' . $e->getMessage(),
            ], 500);
        }
    }
}
