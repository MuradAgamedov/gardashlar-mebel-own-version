<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\LanguageServiceInterface;
use App\Interfaces\Services\Admin\DeliveryPaymentServiceInterface;
use App\Http\Requests\Admin\DeliveryPayment\UpdateDeliveryPaymentRequest;

class DeliveryPaymentController extends Controller
{
    public function __construct(
        protected LanguageServiceInterface $languageService,
        protected DeliveryPaymentServiceInterface $deliveryPaymentService
    )
    {
    }

    public function edit()
    {
        $deliveryPayment = $this->deliveryPaymentService->getFirstOrCreate();
        $languages = $this->languageService->getAllLanguages();
        return view('admin.delivery-payment.edit', compact('deliveryPayment', 'languages'));
    }

    public function update(UpdateDeliveryPaymentRequest $request)
    {
        try {
            $languages = $this->languageService->getAllLanguages();
            $this->deliveryPaymentService->updateDeliveryPayment($request->input('translations'), $languages);

            return redirect()->back()
                ->with('success', __('Delivery & Payment updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating delivery & payment: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }
}
