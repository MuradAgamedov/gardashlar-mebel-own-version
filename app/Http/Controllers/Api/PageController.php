<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PageResource;
use App\Interfaces\Services\Front\PageServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class PageController extends Controller
{
    use LocaleFromHeader;

    protected PageServiceInterface $pageService;

    public function __construct(PageServiceInterface $pageService)
    {
        $this->pageService = $pageService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:pages:all:lang:{$languageId}";

            $pages = Cache::rememberForever($cacheKey, function () {
                return $this->pageService->getActiveOrdered(['translates.language']);
            });

            $resources = $pages->map(function ($page) use ($languageId) {
                $resource = new PageResource($page);
                $resource->languageId = $languageId;
                return $resource;
            });

            return $this->success($resources, 'Pages retrieved successfully');

        } catch (\Throwable $e) {
            report($e);
            return $this->serverError('Error retrieving pages.');
        }
    }

    public function findByKeyword(Request $request, $keyword): JsonResponse
    {
        if (empty($keyword)) {
            return $this->error('Keyword parameter is required', 400);
        }

        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:page:keyword:{$keyword}:lang:{$languageId}";

            $page = Cache::rememberForever($cacheKey, function () use ($keyword) {
                return $this->pageService->getPageByKeyword($keyword);
            });

            if (!$page) {
                return $this->notFound('Page not found with this keyword');
            }

            $resource = new PageResource($page);
            $resource->languageId = $languageId;

            return $this->success($resource, 'Page retrieved successfully');

        } catch (\Throwable $e) {
            report($e);
            return $this->serverError('Error retrieving page.');
        }
    }
}
