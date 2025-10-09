<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CreditCard;
use Illuminate\Http\JsonResponse;

class CreditCardController extends Controller
{
    /**
     * Display a listing of credit cards.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $creditCards = CreditCard::ordered()->get(['id', 'title', 'image', 'months', 'order']);
        
        return response()->json([
            'success' => true,
            'data' => $creditCards->map(function ($card) {
                return [
                    'id' => $card->id,
                    'title' => $card->title,
                    'image' => $card->image ? asset('storage/' . $card->image) : null,
                    'months' => $card->months
                        ? array_map('intval', array_map('trim', explode(',', $card->months)))
                        : [], // boş olarsa boş array qaytarır
                ];
            }),
        ]);
    }
}
