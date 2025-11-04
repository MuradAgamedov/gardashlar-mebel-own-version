<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Services",
 *     description="API Endpoints for Services"
 * )
 */
class ServiceController extends Controller
{
    use LocaleFromHeader;

    public function index(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            
            if (!$languageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language'
                ], 400);
            }

            $cacheKey = "services_api_{$languageId}";
            
            $services = Cache::remember($cacheKey, 3600, function () use ($languageId) {
                return Service::active()
                    ->ordered()
                    ->with(['translates' => function ($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    }])
                    ->get()
                    ->map(function ($service) {
                        $translation = $service->translates->first();
                        
                        return [
                            'id' => $service->id,
                            'slug' => $translation ? $translation->slug : null,
                            'title' => $translation ? $translation->title : 'No title',
                            'card_description' => $translation ? $translation->card_description : 'No description',
                            'advantages_title' => $translation ? $translation->advantages_title : null
                        ];
                    });
            });

            return response()->json([
                'success' => true,
                'message' => 'Services retrieved successfully',
                'data' => $services
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving services: ' . $e->getMessage()
            ], 500);
        }
    }

   public function show(Request $request, $slug)
{
    try {
        $languageId = $this->getLanguageIdFromHeader($request);
        
        if (!$languageId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid language'
            ], 400);
        }

        $cacheKey = "service_detail_{$slug}_{$languageId}";
        
        $serviceData = Cache::remember($cacheKey, 3600, function () use ($slug, $languageId) {
            $service = Service::active()
                ->with([
                    'translates.language',
                    'advantages.translates' => fn($q) => $q->where('language_id', $languageId)
                ])
                ->whereHas('translates', fn($q) => $q->where('slug', $slug))
                ->first();

            if (!$service) {
                return null;
            }

            $translation = $service->translates->firstWhere('language_id', $languageId);

            // 🔹 Servisin bütün dillərdə slug-larını gətiririk
            $slugTranslations = $service->translates->mapWithKeys(function ($t) {
                return [$t->language->lang_code => $t->slug];
            });

            // 🔹 Üstünlükləri (advantages) gətiririk
            $advantages = $service->advantages->map(function ($advantage) {
                $advantageTranslation = $advantage->translates->first();
                return [
                    'id' => $advantage->id,
                    'icon' => $advantage->icon ? asset('storage/' . $advantage->icon) : '',
                    'title' => $advantageTranslation?->title ?? '',
                    'icon_alt_text' => $advantageTranslation?->icon_alt_text ?? '',
                    'description' => $advantageTranslation?->description ?? ''
                ];
            });

            // 🔹 Əsas məlumatlar
            return [
                'id' => $service->id,
                'slug' => $translation?->slug ?? $service->slug,
                'title' => $translation?->title ?? '',
                'card_description' => $translation?->card_description ?? '',
                'main_description' => $translation?->main_description ?? '',
                'form_top_text' => $translation?->form_top_text ?? '',
                'advantages_title' => $translation?->advantages_title ?? '',
                'seo_title' => $translation?->seo_title ?? '',
                'seo_keywords' => $translation?->seo_keywords ?? '',
                'seo_description' => $translation?->seo_description ?? '',
                'advantages' => $advantages,
                'translates' => [
                    'slug' => $slugTranslations
                ]
            ];
        });

        if (!$serviceData) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service retrieved successfully',
            'data' => $serviceData
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving service: ' . $e->getMessage()
        ], 500);
    }
}

} 