<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\ActionServiceInterface;
use App\Http\Resources\Api\ActionResource;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ActionController extends Controller
{
    use LocaleFromHeader;

    protected ActionServiceInterface $actionService;

    public function __construct(ActionServiceInterface $actionService)
    {
        $this->actionService = $actionService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $cacheKey = "actions_{$languageId}" . ($request->filled('company_id') ? "_company_{$request->company_id}" : '');

            $actions = Cache::remember($cacheKey, 3600, function () use ($languageId, $request) {
                return $this->actionService->getActiveCurrentOrdered($languageId, $request->company_id);
            });

            return response()->json([
                'success' => true,
                'message' => 'Actions retrieved successfully',
                'data' => ActionResource::collection($actions),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving actions: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, $id): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $action = Cache::remember("action_{$id}_{$languageId}", 3600, function () use ($id, $languageId) {
                return $this->actionService->getActiveById((int)$id, $languageId);
            });

            if (!$action) {
                return response()->json(['success' => false, 'message' => 'Action not found'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Action retrieved successfully',
                'data' => new ActionResource($action),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving action: ' . $e->getMessage(),
            ], 500);
        }
    }
}
