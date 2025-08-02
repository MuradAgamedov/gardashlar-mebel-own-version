<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * @OA\Tag(
 *     name="Currencies",
 *     description="Currency management endpoints"
 * )
 */
class CurrencyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/currencies",
     *     summary="Get all active currencies",
     *     tags={"Currencies"},
     *     @OA\Response(
     *         response=200,
     *         description="List of currencies",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="code", type="string", example="USD"),
     *                 @OA\Property(property="course", type="number", format="float", example=1.7000)
     *             ))
     *         )
     *     )
     * )
     */
    public function index()
    {
        $currencies = Cache::remember('currencies.all', 3600, function () {
            return Currency::getForFrontend();
        });

        return response()->json([
            'success' => true,
            'data' => $currencies
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/currencies/{code}",
     *     summary="Get currency by code",
     *     tags={"Currencies"},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         description="Currency code (e.g., USD, EUR, AZN)",
     *         @OA\Schema(type="string", example="USD")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Currency details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="code", type="string", example="USD"),
     *                 @OA\Property(property="course", type="number", format="float", example=1.7000)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Currency not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Currency not found")
     *         )
     *     )
     * )
     */
    public function show(string $code)
    {
        $currency = Currency::getByCode(strtoupper($code));

        if (!$currency) {
            return response()->json([
                'success' => false,
                'message' => 'Currency not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'code' => $currency->code,
                'course' => $currency->course
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/currencies/available-codes",
     *     summary="Get all available currency codes",
     *     tags={"Currencies"},
     *     @OA\Response(
     *         response=200,
     *         description="List of available currency codes",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string", example="USD"))
     *         )
     *     )
     * )
     */
    public function getAvailableCodes()
    {
        $codes = Cache::remember('currencies.codes', 3600, function () {
            return Currency::getAvailableCodes();
        });

        return response()->json([
            'success' => true,
            'data' => $codes
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/currencies/by-codes",
     *     summary="Get currencies by array of codes",
     *     tags={"Currencies"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="codes", type="array", @OA\Items(type="string", example="USD"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of currencies",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="code", type="string", example="USD"),
     *                 @OA\Property(property="course", type="number", format="float", example=1.7000)
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function getByCodesArray(Request $request)
    {
        $request->validate([
            'codes' => 'required|array',
            'codes.*' => 'string|max:3'
        ]);

        $codes = array_map('strtoupper', $request->codes);
        $currencies = Currency::active()
            ->whereIn('code', $codes)
            ->ordered()
            ->get()
            ->map(function ($currency) {
                return [
                    'code' => $currency->code,
                    'course' => $currency->course
                ];
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => $currencies
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/currencies/convert",
     *     summary="Convert amount between currencies",
     *     tags={"Currencies"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="amount", type="number", format="float", example=100),
     *             @OA\Property(property="from", type="string", example="USD"),
     *             @OA\Property(property="to", type="string", example="EUR")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Converted amount",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="amount", type="number", format="float", example=100),
     *                 @OA\Property(property="from", type="string", example="USD"),
     *                 @OA\Property(property="to", type="string", example="EUR"),
     *                 @OA\Property(property="converted_amount", type="number", format="float", example=114.11),
     *                 @OA\Property(property="rate", type="number", format="float", example=1.1411)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed")
     *         )
     *     )
     * )
     */
    public function convert(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from' => 'required|string|max:3',
            'to' => 'required|string|max:3'
        ]);

        $amount = $request->amount;
        $fromCode = strtoupper($request->from);
        $toCode = strtoupper($request->to);

        $convertedAmount = Currency::convert($amount, $fromCode, $toCode);

        return response()->json([
            'success' => true,
            'data' => [
                'amount' => $amount,
                'from' => $fromCode,
                'to' => $toCode,
                'converted_amount' => $convertedAmount,
                'rate' => $convertedAmount / $amount
            ]
        ]);
    }
}
