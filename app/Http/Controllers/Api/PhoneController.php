<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PhoneTypeResource;
use App\Interfaces\Services\Front\PhoneServiceInterface;
use App\Interfaces\Services\Front\PhoneTypeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class PhoneController extends Controller
{
    use LocaleFromHeader;

    protected PhoneServiceInterface $phoneService;
    protected PhoneTypeServiceInterface $phoneTypeService;

    public function __construct(
        PhoneServiceInterface $phoneService,
        PhoneTypeServiceInterface $phoneTypeService
    ) {
        $this->phoneService = $phoneService;
        $this->phoneTypeService = $phoneTypeService;
    }

    public function generalPhones(Request $request): JsonResponse
    {
        try {
            $cacheKey = 'api_general_phones';

            $phones = Cache::rememberForever($cacheKey, function () {
                return $this->phoneService->getPhonesByType(3);
            });

            return $this->success($phones, 'General phones retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving general phones: ' . $e->getMessage());
        }
    }

    public function phones(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_phones_{$languageId}";

            Cache::forget($cacheKey);

            $phoneTypes = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->phoneTypeService->getPhonesGroupedByTypes($languageId);
            });

            return $this->success(PhoneTypeResource::collection($phoneTypes), 'Phones retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving phones: ' . $e->getMessage());
        }
    }

    public function specialPhone(Request $request): JsonResponse
    {
        try {
            $cacheKey = 'api_special_phone_type_15';

            $phone = Cache::rememberForever($cacheKey, function () {
                $phoneType = $this->phoneTypeService->getSpecialPhone(15);

                if (!$phoneType || $phoneType->phones->isEmpty()) {
                    return null;
                }

                return $phoneType->phones->first()->phone;
            });

            return $this->success($phone, 'Special phone retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving special phone: ' . $e->getMessage());
        }
    }
}
