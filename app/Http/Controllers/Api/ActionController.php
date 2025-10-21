<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\LocaleFromHeader;
use App\Models\Action;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(name="Actions", description="API Endpoints for Company Actions")
 */
class ActionController extends Controller
{
    use LocaleFromHeader;

 
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

            // Build query
            $query = Action::with(['company', 'translates' => function($q) use ($languageId) {
                $q->where('language_id', $languageId);
            }]);

            // Filter by company if provided
            if ($request->filled('company_id')) {
                $query->where('company_id', $request->company_id);
            }

            // Get actions with cache
            $cacheKey = "actions_{$languageId}" . ($request->filled('company_id') ? "_company_{$request->company_id}" : '');
            $actions = Cache::remember($cacheKey, 3600, function () use ($query) {
                return $query->active()
                    ->current()
                    ->ordered()
                    ->get();
            });

            $actionsData = $actions->map(function ($action) {
                $translation = $action->translates->first();
                
                return [
                    'id' => $action->id,
                    'company_id' => $action->company_id,
                    'icon' => $action->icon,
                    'status' => $action->status,
                    'order' => $action->order,
                    'start_date' => $action->start_date?->format('Y-m-d'),
                    'end_date' => $action->end_date?->format('Y-m-d'),
                    'title' => $translation ? $translation->title : '',
                    'description' => $translation ? $translation->description : null,
                    'image_url' => $translation && $translation->image ? asset('storage/' . $translation->image) : null,
                    'image_alt_text' => $translation ? $translation->image_alt_text : null,
                    'created_at' => $action->created_at,
                    'updated_at' => $action->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Actions retrieved successfully',
                'data' => $actionsData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving actions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/actions/{id}",
     *     operationId="getAction",
     *     tags={"Actions"},
     *     summary="Get action by ID",
     *     description="Returns a specific action with translation based on locale header",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Action ID",
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
     *         description="Action retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Action retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="company_id", type="integer", example=1),
     *                 @OA\Property(property="icon", type="string", example="local_offer", nullable=true),
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="order", type="integer", example=1),
     *                 @OA\Property(property="start_date", type="string", format="date", nullable=true),
     *                 @OA\Property(property="end_date", type="string", format="date", nullable=true),
     *                 @OA\Property(property="title", type="string", example="Action Title"),
     *                 @OA\Property(property="description", type="string", example="Action description", nullable=true),
     *                 @OA\Property(property="image_url", type="string", example="https://example.com/storage/actions/image.jpg", nullable=true),
     *                 @OA\Property(property="image_alt_text", type="string", example="Action Image", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="datetime"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Action not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Action not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error retrieving action")
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

            // Get action with cache
            $action = Cache::remember("action_{$id}_{$languageId}", 3600, function () use ($id, $languageId) {
                return Action::with(['company', 'translates' => function($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                }])
                ->active()
                ->find($id);
            });

            if (!$action) {
                return response()->json([
                    'success' => false,
                    'message' => 'Action not found'
                ], 404);
            }

            $translation = $action->translates->first();

            $actionData = [
                'id' => $action->id,
                'company_id' => $action->company_id,
                'icon' => $action->icon,
                'status' => $action->status,
                'order' => $action->order,
                'start_date' => $action->start_date?->format('Y-m-d'),
                'end_date' => $action->end_date?->format('Y-m-d'),
                'title' => $translation ? $translation->title : '',
                'description' => $translation ? $translation->description : null,
                'image_url' => $translation && $translation->image ? asset('storage/' . $translation->image) : null,
                'image_alt_text' => $translation ? $translation->image_alt_text : null,
                'created_at' => $action->created_at,
                'updated_at' => $action->updated_at,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Action retrieved successfully',
                'data' => $actionData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving action: ' . $e->getMessage()
            ], 500);
        }
    }
}
