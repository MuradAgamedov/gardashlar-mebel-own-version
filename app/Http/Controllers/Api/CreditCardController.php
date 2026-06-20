<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CreditCardResource;
use App\Interfaces\Services\Front\CreditCardServiceInterface;
use Illuminate\Http\JsonResponse;

class CreditCardController extends Controller
{
    protected CreditCardServiceInterface $creditCardService;

    public function __construct(CreditCardServiceInterface $creditCardService)
    {
        $this->creditCardService = $creditCardService;
    }

    public function index(): JsonResponse
    {
        $creditCards = $this->creditCardService->allOrdered();

        return $this->success(CreditCardResource::collection($creditCards));
    }
}
