<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\LanguageServiceInterface;
use App\Http\Resources\Api\LanguageResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class LanguageController extends Controller
{
    use LocaleFromHeader;

    protected LanguageServiceInterface $languageService;

    public function __construct(LanguageServiceInterface $languageService)
    {
        $this->languageService = $languageService;
    }
   
    public function index(Request $request): JsonResponse
    {
        try {
            $languages = Cache::rememberForever('api_languages', function () {
                return $this->languageService->get(['status' => true], ['order' => 'asc']);
            });

            return $this->success(LanguageResource::collection($languages), 'Languages retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving languages: ' . $e->getMessage());
        }
    }
}
