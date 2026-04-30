<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\LanguageRepository;
use App\Repositories\DeliveryPaymentRepository;
use App\Http\Requests\Admin\DeliveryPayment\UpdateDeliveryPaymentRequest;

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

    public function update(UpdateDeliveryPaymentRequest $request)
    {
        try {
            $languages = $this->languageRepository->getAllLanguages();
            $this->repository->updateDeliveryPayment($request->input('translations'), $languages);

            return redirect()->back()
                ->with('success', __('Delivery & Payment updated successfully!'));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('Error updating delivery & payment: :error', ['error' => $e->getMessage()]))
                ->withInput();
        }
    }
}
