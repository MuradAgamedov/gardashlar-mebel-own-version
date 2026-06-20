<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\CertificateResource;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\CertificateServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class CertificateController extends Controller
{
    use LocaleFromHeader;

    protected CertificateServiceInterface $certificateService;

    public function __construct(CertificateServiceInterface $certificateService)
    {
        $this->certificateService = $certificateService;
    }


  
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_certificates_{$languageId}";
            
            $certificates = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->certificateService->getActiveOrderedWithTranslations($languageId);
            });

            return $this->success(CertificateResource::collection($certificates));
        } catch (\Exception $e) {
            return $this->serverError('Error retrieving certificates: ' . $e->getMessage());
        }
    }
} 