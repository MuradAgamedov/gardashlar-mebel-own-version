<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Catalogs",
 *     description="API Endpoints for Catalogs"
 * )
 */
class CatalogController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/catalogs",
     *     summary="Get all active catalogs",
     *     description="Returns list of active catalogs with translated content based on header language",
     *     tags={"Catalogs"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (1=Az, 2=En, 3=Ru)",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Catalogs retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Catalogs retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="catalog_year", type="string", example="2024-2029"),
     *                     @OA\Property(property="catalog_pdf", type="string", example="https://example.com/storage/catalogs/catalog.pdf", nullable=true),
     *                     @OA\Property(property="pdf_filename", type="string", example="catalog.pdf", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No catalogs found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No catalogs found")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $languageId = $this->getLanguageIdFromHeader($request);
        
        $cacheKey = "catalogs_{$languageId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($languageId) {
            $catalogs = Catalog::where('status', true)
                ->orderBy('order')
                ->with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->get();
            
            if ($catalogs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No catalogs found'
                ], 404);
            }
            
            $data = $catalogs->map(function ($catalog) {
                $translation = $catalog->translates->first();
                
                return [
                    'id' => $catalog->id,
                    'catalog_year' => $translation ? $translation->catalog_year : null,
                    'catalog_pdf' => $catalog->catalog_pdf ? asset('storage/' . $catalog->catalog_pdf) : null,
                    'pdf_filename' => $catalog->catalog_pdf ? basename($catalog->catalog_pdf) : null,
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Catalogs retrieved successfully',
                'data' => $data
            ]);
        });
    }
}