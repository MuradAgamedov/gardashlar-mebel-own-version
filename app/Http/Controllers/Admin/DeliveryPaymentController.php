<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPayment;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Repositories\LanguageRepository;

class DeliveryPaymentController extends Controller
{
    public function __construct(public LanguageRepository $languageRepository)
    {
    }

    /**
     * Display the specified resource.
     */
    public function edit()
    {
        $deliveryPayment = DeliveryPayment::with(['translates.language'])->first();
        
        if (!$deliveryPayment) {
            $deliveryPayment = DeliveryPayment::create(['status' => true]);
        }
        
        $languages = $this->languageRepository->getAllLanguages();
        
        return view('admin.delivery-payment.edit', compact('deliveryPayment', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $languages = $this->languageRepository->getAllLanguages();
        
        // Translation validation
        foreach ($languages as $language) {
            $request->validate([
                "translations.{$language->id}.content" => 'required|string|max:5000',
            ], [
                "translations.{$language->id}.content.required" => "Content is required for {$language->lang_code}.",
                "translations.{$language->id}.content.max" => "Content may not be greater than 5000 characters for {$language->lang_code}.",
            ]);
        }

        try {
            $deliveryPayment = DeliveryPayment::first();
            
            if (!$deliveryPayment) {
                $deliveryPayment = DeliveryPayment::create([
                    'status' => true,
                ]);
            }

            // Update translations
            foreach ($languages as $language) {
                $translationData = $request->input("translations.{$language->id}", []);
                
                $deliveryPayment->translates()->updateOrCreate(
                    ['language_id' => $language->id],
                    [
                        'content' => trim($translationData['content'] ?? ''),
                    ]
                );
            }

            // Clear cache for all languages
            $languages = Language::getActiveLanguages();
            foreach ($languages as $language) {
                Cache::forget("delivery_payment_{$language->id}");
            }

            return redirect()->back()
                ->with('success', 'Delivery & Payment updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating delivery & payment: ' . $e->getMessage())
                ->withInput();
        }
    }
}
