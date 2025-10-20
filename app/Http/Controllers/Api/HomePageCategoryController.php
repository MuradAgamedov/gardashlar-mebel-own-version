<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomePageCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class HomePageCategoryController extends Controller
{
    use LocaleFromHeader;

  
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api:homepage-categories:lang:{$languageId}";

            $homePageCategories = Cache::rememberForever($cacheKey, function () use ($languageId) {
                return HomePageCategory::active()
                    ->ordered()
                    ->with(['productCategory.translates' => fn($q) => $q->where('language_id', $languageId)])
                    ->get()
                    ->map(function ($homePageCategory) use ($languageId) {
                        $productCategory = $homePageCategory->productCategory;
                        $translate = $productCategory->translates->first();
                        
                        return [
                            'id' => $productCategory->id,
                            'image_url' => asset('storage/' . $productCategory->image),
                            'title' => $translate->title ?? null,
                            'slug' => $translate->slug ?? null,
                            'parentSlug' =>  $productCategory->parent_id == null ? null : $productCategory->parent->translate($languageId)->slug
                        ];
                    });
            });
            

            return response()->json([
                'success' => true,
                'data' => $homePageCategories
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving homepage categories: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }


} 