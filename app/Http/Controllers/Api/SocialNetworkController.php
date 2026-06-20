<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SocialNetwork\GetByTypesRequest;
use App\Http\Resources\Api\SocialNetworkResource;
use App\Interfaces\Services\Front\SocialNetworkServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SocialNetworkController extends Controller
{
    protected SocialNetworkServiceInterface $socialNetworkService;

    public function __construct(SocialNetworkServiceInterface $socialNetworkService)
    {
        $this->socialNetworkService = $socialNetworkService;
    }
   
    public function index(): JsonResponse
    {
        $cacheKey = 'social_networks_active';
        
        $socialNetworks = Cache::remember($cacheKey, 3600, function () {
            return $this->socialNetworkService->get(['status' => true], ['order' => 'asc']);
        });

        return $this->success(SocialNetworkResource::collection($socialNetworks));
    }

    public function show(string $type): JsonResponse
    {
        $cacheKey = "social_network_{$type}";
        
        $socialNetwork = Cache::remember($cacheKey, 3600, function () use ($type) {
            return $this->socialNetworkService->getByType($type);
        });

        if (!$socialNetwork) {
            return $this->notFound('Social network not found');
        }

        return $this->success(new SocialNetworkResource($socialNetwork));
    }

    public function getByTypesArray(GetByTypesRequest $request): JsonResponse
    {
        $types = $request->types;
        $cacheKey = 'social_networks_by_types_' . md5(serialize($types));
        
        $socialNetworks = Cache::remember($cacheKey, 3600, function () use ($types) {
            return $this->socialNetworkService->getByTypes($types)->keyBy('type');
        });

        $data = $socialNetworks->map(fn($sn) => new SocialNetworkResource($sn));

        return $this->success($data);
    }

    public function getAvailableTypes(): JsonResponse
    {
        $cacheKey = 'social_networks_available_types';
        
        $types = Cache::remember($cacheKey, 3600, function () {
            return $this->socialNetworkService->getAvailableTypes();
        });

        return $this->success($types);
    }
}
