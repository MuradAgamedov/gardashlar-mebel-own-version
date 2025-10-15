<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\LanguageRepository;
use App\Repositories\DeliveryPaymentRepository;

class DeliveryPaymentController extends Controller
{
    public function __construct(
        public LanguageRepository $languageRepository,
        public DeliveryPaymentRepository $repository
    )
    {
    }

    public function edit()
    {
        $deliveryPayment = $this->repository->getFirstOrCreate();
        $languages = $this->languageRepository->getAllLanguages();
        return view('admin.delivery-payment.edit', compact('deliveryPayment', 'languages'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'translations' => 'required|array',
            'translations.*.content' => 'required|string|max:5000',
        ]);

        try {
            $languages = $this->languageRepository->getAllLanguages();
            $this->repository->updateDeliveryPayment($request->input('translations'), $languages);

            return redirect()->back()
                ->with('success', 'Delivery & Payment updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating delivery & payment: ' . $e->getMessage())
                ->withInput();
        }
    }
}
