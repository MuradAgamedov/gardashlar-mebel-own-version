<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ReportResource;
use App\Interfaces\Services\Front\ReportServiceInterface;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class ReportController extends Controller
{
    use LocaleFromHeader;

    protected ReportServiceInterface $reportService;

    public function __construct(ReportServiceInterface $reportService)
    {
        $this->reportService = $reportService;
    }


   
    public function index(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);

            $reports = Cache::remember("reports_{$languageId}", 3600, function () use ($languageId) {
                return $this->reportService->getActiveOrderedWithTranslations($languageId);
            });

            return response()->json([
                'success' => true,
                'message' => 'Reports retrieved successfully',
                'data' => ReportResource::collection($reports)
            ]);

        } catch (\Exception $e) {
            \Log::error('Error retrieving reports: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error retrieving reports',
                'data' => null
            ], 500);
        }
    }
}
