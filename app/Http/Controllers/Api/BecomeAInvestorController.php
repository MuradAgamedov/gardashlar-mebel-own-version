<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Front\BecomeAInvestorServiceInterface;
use App\Models\BecomeAInvestorAdvantage;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Become A Investor", description="API Endpoints for Become A Investor")
 */
class BecomeAInvestorController extends Controller
{
    use LocaleFromHeader;

    protected BecomeAInvestorServiceInterface $becomeAInvestorService;

    public function __construct(BecomeAInvestorServiceInterface $becomeAInvestorService)
    {
        $this->becomeAInvestorService = $becomeAInvestorService;
    }

   
    public function index(Request $request): JsonResponse
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            $cacheKey = "api_become_a_investor_{$languageId}";
            
            $data = Cache::rememberForever($cacheKey, function () use ($languageId) {
                $becomeAInvestor = BecomeAInvestor::first();
                
                if (!$becomeAInvestor) {
                    return null;
                }

                $translation = $becomeAInvestor->translates()->where('language_id', $languageId)->first();

                $advantages = BecomeAInvestorAdvantage::with(['translates' => function($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])->where('status', true)->orderBy('order')->get()->map(function ($advantage) {
                    $translation = $advantage->translates->first();
                    
                    return [
                        'id' => $advantage->id,
                        'icon' => $advantage->icon ? asset('storage/' . $advantage->icon) : null,
                        'title' => $translation ? $translation->title : '',
                        'description' => $translation ? $translation->description : '',
                        'icon_alt_text' => $translation ? $translation->icon_alt_text : ''
                    ];
                });

                return [
                    'id' => $becomeAInvestor->id,
                    'content' => $translation ? $translation->content : '',
                    'advantages' => $advantages
                ];
            });

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Become a investor data not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Become a investor data retrieved successfully',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving become a investor data: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
