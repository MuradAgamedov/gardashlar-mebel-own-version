<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CreditCardResource;
use App\Models\CreditCard;
use Illuminate\Http\JsonResponse;

class CreditCardController extends Controller
{
    public function index(): JsonResponse
    {
        $creditCards = CreditCard::ordered()->get(['id', 'title', 'image', 'months', 'order']);

        return $this->success(CreditCardResource::collection($creditCards));
    }
}
