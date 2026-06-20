<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Subscriber\StoreSubscriberRequest;
use App\Interfaces\Services\Front\SubscriberServiceInterface;
use Illuminate\Http\JsonResponse;

class SubscriberController extends Controller
{
    protected SubscriberServiceInterface $subscriberService;

    public function __construct(SubscriberServiceInterface $subscriberService)
    {
        $this->subscriberService = $subscriberService;
    }

    public function store(StoreSubscriberRequest $request): JsonResponse
    {
        try {
            $subscriber = $this->subscriberService->create([
                'email' => $request->email,
            ]);

            return response()->json([
                'success' => true,
                'message' => dict('subscribe_success'),
                'data' => [
                    'id' => $subscriber->id,
                    'email' => $subscriber->email,
                    'subscribed_at' => $subscriber->created_at->format('Y-m-d H:i:s')
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error subscribing email: ' . $e->getMessage()
            ], 500);
        }
    }
}
