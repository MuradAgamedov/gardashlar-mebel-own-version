<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Subscriber\StoreSubscriberRequest;
use App\Interfaces\Services\Front\SubscriberServiceInterface;
use App\Http\Resources\Api\SubscriberResource;
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

            return $this->success(new SubscriberResource($subscriber), dict('subscribe_success'));

        } catch (\Exception $e) {
            return $this->serverError('Error subscribing email: ' . $e->getMessage());
        }
    }
}
