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

    /**
     * @OA\Get(
     *     path="/api/services",
     *     summary="Get all active services",
     *     description="Returns list of active services with translated title and card_description",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Services retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="slug", type="string", example="mebel-istehsali"),
     *                     @OA\Property(property="title", type="string", example="Mebel İstehsalı"),
     *                     @OA\Property(property="card_description", type="string", example="Yüksək keyfiyyətli mebel istehsalı")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid language",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid language")
     *         )
     *     )
     * )
     */
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
                            'card_description' => $translation ? $translation->card_description : 'No description'
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

    /**
     * @OA\Get(
     *     path="/api/services/{slug}",
     *     summary="Get service details by slug",
     *     description="Returns detailed service information including advantages by slug",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         description="Service slug",
     *         required=true,
     *         @OA\Schema(type="string", example="construction-services")
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Service retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="slug", type="string", example="construction-services"),
     *                 @OA\Property(property="title", type="string", example="Tikinti Xidmətləri"),
     *                 @OA\Property(property="card_description", type="string", example="Keyfiyyətli tikinti və inşaat işləri"),
     *                 @OA\Property(property="main_description", type="string", example="Biz müasir texnologiyalardan istifadə edərək..."),
     *                 @OA\Property(property="seo_title", type="string", example="Tikinti Xidmətləri"),
     *                 @OA\Property(property="seo_keywords", type="string", example="tikinti, inşaat, bina"),
     *                 @OA\Property(property="seo_description", type="string", example="Professional tikinti xidmətləri"),
     *                 @OA\Property(
     *                     property="advantages",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="icon", type="string", example="services/icons/quality-assurance.svg"),
     *                         @OA\Property(property="title", type="string", example="Keyfiyyət Təminatı"),
     *                         @OA\Property(property="icon_alt_text", type="string", example="Keyfiyyət təminatı ikonu"),
     *                         @OA\Property(property="description", type="string", example="Yüksək keyfiyyət standartları")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Service not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Invalid language",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid language")
     *         )
     *     )
     * )
     */
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
                    ->with(['translates' => function ($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    }, 'advantages.translates' => function ($query) use ($languageId) {
                        $query->where('language_id', $languageId);
                    }])
                    ->whereHas('translates', function ($query) use ($slug, $languageId) {
                        $query->where('slug', $slug)->where('language_id', $languageId);
                    })
                    ->first();

                if (!$service) {
                    return null;
                }

                $translation = $service->translates->first();
                
                $advantages = $service->advantages->map(function ($advantage) {
                    $advantageTranslation = $advantage->translates->first();
                    
                    return [
                        'id' => $advantage->id,
                        'icon' => $advantage->icon ? asset('storage/' . $advantage->icon) : '',
                        'title' => $advantageTranslation ? $advantageTranslation->title : '',
                        'icon_alt_text' => $advantageTranslation ? $advantageTranslation->icon_alt_text : '',
                        'description' => $advantageTranslation ? $advantageTranslation->description : ''
                    ];
                });

                return [
                    'id' => $service->id,
                    'slug' => $translation ? $translation->slug : $service->slug,
                    'title' => $translation ? $translation->title : '',
                    'card_description' => $translation ? $translation->card_description : '',
                    'main_description' => $translation ? $translation->main_description : '',
                    'seo_title' => $translation ? $translation->seo_title : '',
                    'seo_keywords' => $translation ? $translation->seo_keywords : '',
                    'seo_description' => $translation ? $translation->seo_description : '',
                    'advantages' => $advantages
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