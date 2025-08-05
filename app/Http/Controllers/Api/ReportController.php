<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Traits\LocaleFromHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Reports",
 *     description="API Endpoints for Reports"
 * )
 */
class ReportController extends Controller
{
    use LocaleFromHeader;

    /**
     * @OA\Get(
     *     path="/api/reports",
     *     summary="Get all active reports",
     *     description="Returns a list of all active reports with translations",
     *     tags={"Reports"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Reports retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="order", type="integer", example=1),
     *                     @OA\Property(property="status", type="boolean", example=true),
     *                     @OA\Property(property="pdf_url", type="string", example="https://example.com/storage/reports/report.pdf"),
     *                     @OA\Property(property="title", type="string", example="2021 Financial Report"),
     *                     @OA\Property(property="description", type="string", example="Annual financial report for 2021"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving reports"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $languageId = $this->getLanguageIdFromHeader($request);
            
            $reports = Cache::remember("reports_{$languageId}", 3600, function () use ($languageId) {
                return Report::with(['translates' => function ($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }])
                ->where('status', true)
                ->orderBy('order')
                ->get()
                ->map(function ($report) {
                    $translation = $report->translates->first();
                    
                    return [
                        'id' => $report->id,
                        'order' => $report->order,
                        'status' => $report->status,
                        'pdf_url' => $report->pdf_url,
                        'title' => $translation ? $translation->title : null,
                        'description' => $translation ? $translation->description : null,
                        'created_at' => $report->created_at,
                        'updated_at' => $report->updated_at,
                    ];
                });
            });

            return response()->json([
                'success' => true,
                'message' => 'Reports retrieved successfully',
                'data' => $reports
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
