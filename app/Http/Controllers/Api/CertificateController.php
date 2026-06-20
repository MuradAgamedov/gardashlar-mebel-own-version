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


    /**
     * @OA\Get(
     *     path="/api/certificates",
     *     operationId="getCertificates",
     *     tags={"Certificates"},
     *     summary="Get all certificates",
     *     description="Returns a list of all active certificates, translated based on locale header",
     *     @OA\Parameter(name="locale", in="header", description="Language ID", required=false, @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Successful response", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=true), @OA\Property(property="data", type="array", @OA\Items(type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="image_url", type="string", example="https://example.com/storage/certificates/certificate.jpg", nullable=true), @OA\Property(property="title", type="string", example="ISO 14001:2015 Certificate"), @OA\Property(property="date", type="string", example="2022-01-15"))))),
     *     @OA\Response(response=500, description="Internal server error", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string", example="Error retrieving certificates")))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_certificates_{$languageId}";
            
            $certificates = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->certificateService->getActiveOrderedWithTranslations($languageId);
            });

            return response()->json(['success' => true, 'data' => CertificateResource::collection($certificates)], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error retrieving certificates: ' . $e->getMessage(), 'data' => []], 500);
        }
    }
} 