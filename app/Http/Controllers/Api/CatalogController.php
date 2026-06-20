<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\CatalogServiceInterface;
use App\Http\Resources\Api\CatalogResource;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;


class CatalogController extends Controller
{
    use LocaleFromHeader;

    protected CatalogServiceInterface $catalogService;

    public function __construct(CatalogServiceInterface $catalogService)
    {
        $this->catalogService = $catalogService;
    }


    public function index(Request $request): JsonResponse
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        
        $cacheKey = "catalogs_{$languageId}";
        
        $catalogs = Cache::remember($cacheKey, 3600, function () use ($languageId) {
            return $this->catalogService->getActiveOrderedWithTranslations($languageId);
        });

        if ($catalogs->isEmpty()) {
            return $this->notFound('No catalogs found');
        }

        return $this->success(CatalogResource::collection($catalogs), 'Catalogs retrieved successfully');
    }

    public function show(Request $request, $id): JsonResponse
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        
        $cacheKey = "catalog_{$id}_{$languageId}";
        
        $catalog = Cache::remember($cacheKey, 3600, function () use ($id, $languageId) {
            return $this->catalogService->first([
                'translates' => fn($q) => $q->where('language_id', $languageId)
            ], [
                'id' => $id,
                'status' => true
            ]);
        });

        if (!$catalog) {
            return $this->notFound('Catalog not found');
        }

        return $this->success(new CatalogResource($catalog), 'Catalog retrieved successfully');
    }
}