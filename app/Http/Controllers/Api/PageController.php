<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class PageController extends Controller
{
    use LocaleFromHeader;

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:pages:all:lang:{$languageId}";

            $pages = Cache::rememberForever($cacheKey, function () {
                return Page::where('status', true)
                    ->with(['translates.language'])
                    ->get();
            });

            $resources = $pages->map(function ($page) use ($languageId) {
                $resource = new PageResource($page);
                $resource->languageId = $languageId;
                return $resource;
            });

            return response()->json(['success' => true, 'data' => $resources]);

        } catch (\Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Error retrieving pages.', 'data' => []], 500);
        }
    }

    public function findByKeyword(Request $request, $keyword): JsonResponse
    {
        if (empty($keyword)) {
            return response()->json(['success' => false, 'message' => 'Keyword parameter is required', 'data' => null], 400);
        }

        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:page:keyword:{$keyword}:lang:{$languageId}";

            $page = Cache::rememberForever($cacheKey, function () use ($keyword) {
                return Page::where('status', true)
                    ->where('keyword', $keyword)
                    ->with(['translates.language'])
                    ->first();
            });

            if (!$page) {
                return response()->json(['success' => false, 'message' => 'Page not found with this keyword', 'data' => null], 404);
            }

            $resource = new PageResource($page);
            $resource->languageId = $languageId;

            return response()->json(['success' => true, 'data' => $resource]);

        } catch (\Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Error retrieving page.', 'data' => null], 500);
        }
    }
}
