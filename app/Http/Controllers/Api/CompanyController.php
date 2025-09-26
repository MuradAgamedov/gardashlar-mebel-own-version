<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\LocaleFromHeader;
use App\Models\Company;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(name="Companies", description="API Endpoints for Companies")
 */
class CompanyController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/companies",
     *     operationId="getCompanies",
     *     tags={"Companies"},
     *     summary="Get all companies",
     *     description="Returns a list of all active companies with translations based on locale header",
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (e.g., 1 for Azerbaijani, 2 for English, 3 for Russian)",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Companies retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Companies retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="card_image_url", type="string", example="https://example.com/storage/companies/card_image.jpg", nullable=true),
     *                     @OA\Property(property="card_image_alt_text", type="string", example="Card Image", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving companies")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            // Get language ID from header
            $languageId = $this->getLanguageIdFromHeader($request);
            
            // Validate language ID
            $language = Language::find($languageId);
            if (!$language) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language ID'
                ], 400);
            }

            // Get companies with cache
            $companies = Cache::remember("companies_{$languageId}", 3600, function () use ($languageId) {
                return Company::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->active()
                ->ordered()
                ->get();
            });

            $companiesData = $companies->map(function ($company) {
                $translation = $company->translates->first();
                
                return [
                    'card_image_url' => $translation && $translation->card_image ? asset('storage/' . $translation->card_image) : null,
                    'card_image_alt_text' => $translation ? $translation->card_image_alt_text : null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Companies retrieved successfully',
                'data' => $companiesData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving companies: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/companies/{id}",
     *     operationId="getCompany",
     *     tags={"Companies"},
     *     summary="Get company by ID",
     *     description="Returns a specific company with translation based on locale header",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Company ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="locale",
     *         in="header",
     *         description="Language ID (e.g., 1 for Azerbaijani, 2 for English, 3 for Russian)",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Company retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Company retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="card_image_url", type="string", example="https://example.com/storage/companies/card_image.jpg", nullable=true),
     *                 @OA\Property(property="card_image_alt_text", type="string", example="Card Image", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Company not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Company not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving company")
     *         )
     *     )
     * )
     */
    public function show(Request $request, $id)
    {
        try {
            // Get language ID from header
            $languageId = $this->getLanguageIdFromHeader($request);
            
            // Validate language ID
            $language = Language::find($languageId);
            if (!$language) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language ID'
                ], 400);
            }

            // Get company with cache
            $company = Cache::remember("company_{$id}_{$languageId}", 3600, function () use ($id, $languageId) {
                return Company::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->active()
                ->find($id);
            });

            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company not found'
                ], 404);
            }

            $translation = $company->translates->first();

            $companyData = [
                'card_image_url' => $translation && $translation->card_image ? asset('storage/' . $translation->card_image) : null,
                'card_image_alt_text' => $translation ? $translation->card_image_alt_text : null,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Company retrieved successfully',
                'data' => $companyData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving company: ' . $e->getMessage()
            ], 500);
        }
    }
}
