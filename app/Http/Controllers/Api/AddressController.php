<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\AddressResource;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\AddressServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class AddressController extends Controller
{
    use LocaleFromHeader;

    protected AddressServiceInterface $addressService;

    public function __construct(AddressServiceInterface $addressService)
    {
        $this->addressService = $addressService;
    }


    
    public function index(Request $request): JsonResponse
    {
        try {
            // Get language ID from header using trait
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $cacheKey = "api_addresses_{$languageId}";
            
            $addresses = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->addressService->getActiveOrderedWithTranslations($languageId, [
                    'addressType.translates' => function($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    }
                ]);
            });

            return $this->success(AddressResource::collection($addresses));

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving addresses: ' . $e->getMessage());
        }
    }
} 