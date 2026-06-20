<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Currency\GetByCodesRequest;
use App\Http\Requests\Api\Currency\ConvertCurrencyRequest;
use App\Http\Resources\Api\CurrencyResource;
use App\Models\Currency;
use Illuminate\Support\Facades\Cache;


class CurrencyController extends Controller
{
   
    public function index()
    {
        $currencies = Cache::remember('currencies.all', 3600, function () {
            return Currency::active()->ordered()->get();
        });

        return response()->json([
            'success' => true,
            'data' => CurrencyResource::collection($currencies)
        ]);
    }

   
    public function show(string $code)
    {
        $currency = Currency::getByCode(strtoupper($code));

        if (!$currency) {
            return $this->notFound('Currency not found');
        }

        return $this->success(new CurrencyResource($currency));
    }

  
    public function getAvailableCodes()
    {
        $codes = Cache::remember('currencies.codes', 3600, function () {
            return Currency::getAvailableCodes();
        });

        return $this->success($codes);
    }

    public function getByCodesArray(GetByCodesRequest $request)
    {
        $codes = array_map('strtoupper', $request->codes);
        $currencies = Currency::active()
            ->whereIn('code', $codes)
            ->ordered()
            ->get();

        return $this->success(CurrencyResource::collection($currencies));
    }


    public function convert(ConvertCurrencyRequest $request)
    {
        $amount = $request->amount;
        $fromCode = strtoupper($request->from);
        $toCode = strtoupper($request->to);

        $convertedAmount = Currency::convert($amount, $fromCode, $toCode);

        return $this->success([
            'amount' => $amount,
            'from' => $fromCode,
            'to' => $toCode,
            'converted_amount' => $convertedAmount,
            'rate' => $convertedAmount / $amount
        ]);
    }
}
