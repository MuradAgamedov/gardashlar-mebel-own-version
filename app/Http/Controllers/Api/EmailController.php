<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Traits\LocaleFromHeader;
use OpenApi\Annotations as OA;

class EmailController extends Controller
{
    use LocaleFromHeader;

    public function index(Request $request): JsonResponse
    {
        try {
            $cacheKey = 'api_emails';
            
            $emails = Cache::rememberForever($cacheKey, function () {
                return Email::select(['id', 'email'])
                    ->where('status', true)
                    ->orderBy('order')
                    ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $emails
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving emails: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
} 