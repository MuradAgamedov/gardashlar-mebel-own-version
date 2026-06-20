<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SimpleImage\GetByKeywordsRequest;
use App\Http\Resources\Api\SimpleImageResource;
use App\Interfaces\Services\Front\SimpleImageServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;

class SimpleImageController extends Controller
{
    use LocaleFromHeader;

    protected SimpleImageServiceInterface $simpleImageService;

    public function __construct(SimpleImageServiceInterface $simpleImageService)
    {
        $this->simpleImageService = $simpleImageService;
    }
   
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:simple_images:all:lang:{$languageId}";

            $images = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return $this->simpleImageService->get([], [], ['translates' => fn($q) => $q->where('language_id', $languageId)]);
            });

            return $this->success(SimpleImageResource::collection($images), 'Images retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving images: ' . $e->getMessage());
        }
    }

    public function show(Request $request, string $keyword): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $image = $this->simpleImageService->first(
                ['translates' => fn($q) => $q->where('language_id', $languageId)],
                ['keyword' => $keyword]
            );

            if (!$image) {
                return $this->notFound('Image not found');
            }

            return $this->success(new SimpleImageResource($image), 'Image retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving image: ' . $e->getMessage());
        }
    }

    public function getByKeywordsArray(GetByKeywordsRequest $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $keywords = $request->input('keywords');
            
            $images = $this->simpleImageService->get(
                fn($q) => $q->whereIn('keyword', $keywords),
                [],
                ['translates' => fn($q) => $q->where('language_id', $languageId)]
            )
            ->mapWithKeys(function ($image) {
                $translate = $image->translates->first();
                return [
                    $image->keyword => [
                        'image_url'      => $image->image_url,
                        'image_go_url'   => $translate?->image_go_url,
                        'image_alt_text' => $translate?->image_alt_text,
                    ]
                ];
            });

            return $this->success($images, 'Images retrieved successfully');

        } catch (\Exception $e) {
            return $this->serverError('Error retrieving images: ' . $e->getMessage());
        }
    }
}
