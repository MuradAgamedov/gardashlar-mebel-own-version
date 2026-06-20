<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ContentPageResource;
use App\Interfaces\Services\Front\CustomerRightsServiceInterface;
use App\Interfaces\Services\Front\LanguageServiceInterface;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CustomerRightsController extends Controller
{
    use LocaleFromHeader;

    protected CustomerRightsServiceInterface $customerRightsService;
    protected LanguageServiceInterface $languageService;

    public function __construct(
        CustomerRightsServiceInterface $customerRightsService,
        LanguageServiceInterface $languageService
    ) {
        $this->customerRightsService = $customerRightsService;
        $this->languageService = $languageService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $language = $this->languageService->first([], ['id' => $languageId]);
            if (!$language) {
                return $this->error('Invalid language ID', 400);
            }

            $customerRights = Cache::rememberForever("customer_rights_{$languageId}", function () use ($languageId) {
                return $this->customerRightsService->first(['translates' => fn($q) => $q->where('language_id', $languageId)]);
            });

            if (!$customerRights) {
                return $this->notFound('Customer rights not found');
            }

            return $this->success(new ContentPageResource($customerRights), 'Customer rights retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving customer rights: ' . $e->getMessage());
        }
    }
}
